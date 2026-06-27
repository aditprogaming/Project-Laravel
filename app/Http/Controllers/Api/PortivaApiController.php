<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PortivaApiController extends Controller
{
    public function store(Request $request)
    {
        $loginResponse = $this->ensureLoggedIn();
        if ($loginResponse instanceof JsonResponse) {
            return $loginResponse;
        }

        $validation = $this->validatePortfolio($request);
        if ($validation instanceof JsonResponse) {
            return $validation;
        }

        $portfolio = $this->savePortfolio($request, $validation);
        if ($portfolio instanceof JsonResponse) {
            return $portfolio;
        }

        return $this->successResponse(
            'Data portfolio berhasil disimpan.',
            $this->formatPortfolio($portfolio->load('user')),
            '/portofolio?template=' . ($portfolio->template + 1),
        );
    }

    public function update(Request $request, int $id)
    {
        $loginResponse = $this->ensureLoggedIn();
        if ($loginResponse instanceof JsonResponse) {
            return $loginResponse;
        }

        if (!Schema::hasTable('portfolios')) {
            return $this->errorResponse('Tabel portfolios belum tersedia.', 404);
        }

        $portfolio = Portfolio::find($id);

        if (!$portfolio || !$this->canManagePortfolio($portfolio)) {
            return $this->errorResponse('Portfolio tidak ditemukan atau Anda tidak memiliki akses.', 404);
        }

        $validation = $this->validatePortfolio($request);
        if ($validation instanceof JsonResponse) {
            return $validation;
        }

        $portfolio = $this->savePortfolio($request, $validation, $portfolio);
        if ($portfolio instanceof JsonResponse) {
            return $portfolio;
        }

        return $this->successResponse(
            'Data portfolio berhasil diperbarui.',
            $this->formatPortfolio($portfolio->load('user')),
            '/portofolio?template=' . ($portfolio->template + 1),
        );
    }

    public function destroy(int $id)
    {
        $loginResponse = $this->ensureLoggedIn();
        if ($loginResponse instanceof JsonResponse) {
            return $loginResponse;
        }

        if (!Schema::hasTable('portfolios')) {
            return $this->errorResponse('Tabel portfolios belum tersedia.', 404);
        }

        $portfolio = Portfolio::find($id);

        if (!$portfolio || !$this->canManagePortfolio($portfolio)) {
            return $this->errorResponse('Portfolio tidak ditemukan atau Anda tidak memiliki akses.', 404);
        }

        $this->deletePortfolioPhoto($portfolio->photo);
        $portfolio->delete();

        return $this->successResponse('Portfolio berhasil dihapus.', null, '/akun');
    }

    public function profiles()
    {
        $profiles = Schema::hasTable('portfolios')
            ? Portfolio::with('user')->latest()->get()
            : collect();

        return $this->successResponse('Daftar profil berhasil dimuat.', $profiles->map(fn (Portfolio $portfolio) => $this->formatPortfolio($portfolio)));
    }

    public function show(int $id)
    {
        if (!Schema::hasTable('portfolios')) {
            return $this->errorResponse('Tabel portfolios belum tersedia.', 404);
        }

        $portfolio = Portfolio::with('user')->find($id);

        if (!$portfolio) {
            return $this->errorResponse('Portfolio tidak ditemukan.', 404);
        }

        return $this->successResponse('Detail portfolio berhasil dimuat.', $this->formatPortfolio($portfolio));
    }

    public function account()
    {
        if (!Session::has('user') && !Session::has('admin')) {
            return $this->errorResponse('Silakan login terlebih dahulu.', 401);
        }

        $user = Session::has('admin')
            ? (object) [
                'id' => null,
                'name' => 'Admin Portiva',
                'email' => 'admin@portiva.test',
                'avatar' => null,
                'role' => 'admin',
            ]
            : User::find(Session::get('user'));

        $profiles = Schema::hasTable('portfolios')
            ? (Session::has('admin')
                ? Portfolio::with('user')->latest()->get()
                : Portfolio::with('user')->where('user_id', $user?->id)->latest()->get())
            : collect();

        return $this->successResponse('Informasi akun berhasil dimuat.', [
            'user' => $this->formatUser($user),
            'profiles' => $profiles->map(fn (Portfolio $portfolio) => $this->formatPortfolio($portfolio)),
        ]);
    }

    public function users()
    {
        if (!Session::has('admin')) {
            return $this->errorResponse('Hanya admin yang dapat mengakses data pengguna.', 403);
        }

        $users = User::orderByDesc('created_at')->get();

        return $this->successResponse('Daftar pengguna berhasil dimuat.', $users->map(fn (User $user) => $this->formatUser($user)));
    }

    private function ensureLoggedIn(): ?JsonResponse
    {
        if (Session::has('user') || Session::has('admin')) {
            return null;
        }

        return $this->errorResponse('Silakan login terlebih dahulu.', 401);
    }

    private function validatePortfolio(Request $request): array|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'profession' => 'required|string|max:255',
            'about' => 'required|string',
            'skills' => 'required|string',
            'experience' => 'required|string',
            'contact' => 'required|string',
            'template' => 'nullable|integer',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors()->toArray());
        }

        return $validator->validated();
    }

    private function savePortfolio(Request $request, array $data, ?Portfolio $portfolio = null): Portfolio|JsonResponse
    {
        $userId = Session::get('user');

        if (!$userId) {
            return $this->errorResponse('Login dibutuhkan untuk menyimpan portfolio.', 401);
        }

        $template = (int) ($data['template'] ?? $portfolio?->template ?? 1);
        $photoPath = $portfolio?->photo;

        if ($request->hasFile('photo')) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            $photoPath = $request->file('photo')->store('portfolios', 'public');
        }

        $payload = [
            'user_id' => $userId,
            'name' => $data['name'],
            'profession' => $data['profession'],
            'about' => $data['about'],
            'skills' => $data['skills'],
            'experience' => $data['experience'],
            'contact' => $data['contact'],
            'template' => $template,
            'photo' => $photoPath,
        ];

        if ($portfolio) {
            $portfolio->update($payload);
        } else {
            $portfolio = Portfolio::create($payload);
        }

        if ($request->boolean('use_for_account') && $photoPath) {
            $user = User::find($userId);
            if ($user) {
                $user->avatar = $photoPath;
                $user->save();
            }
        }

        return $portfolio;
    }

    private function canManagePortfolio(Portfolio $portfolio): bool
    {
        return Session::has('admin') || (Session::has('user') && (int) Session::get('user') === (int) $portfolio->user_id);
    }

    private function deletePortfolioPhoto(?string $photoPath): void
    {
        if ($photoPath && Storage::disk('public')->exists($photoPath)) {
            Storage::disk('public')->delete($photoPath);
        }
    }

    private function successResponse(string $message, mixed $data = null, ?string $redirectTo = null): JsonResponse
    {
        return response()->json(array_filter([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'redirect_to' => $redirectTo,
        ], static fn ($value) => $value !== null), 200);
    }

    private function errorResponse(string $message, int $status = 422, array $errors = []): JsonResponse
    {
        return response()->json(array_filter([
            'success' => false,
            'message' => $message,
            'errors' => $errors ?: null,
        ], static fn ($value) => $value !== null), $status);
    }

    private function formatPortfolio(Portfolio $portfolio): array
    {
        return [
            'id' => $portfolio->id,
            'user_id' => $portfolio->user_id,
            'name' => $portfolio->name,
            'profession' => $portfolio->profession,
            'about' => $portfolio->about,
            'skills' => $portfolio->skills,
            'experience' => $portfolio->experience,
            'contact' => $portfolio->contact,
            'template' => $portfolio->template,
            'photo' => $portfolio->photo,
            'photo_url' => $this->resolveFileUrl($portfolio->photo),
            'created_at' => $portfolio->created_at,
            'updated_at' => $portfolio->updated_at,
            'user' => $portfolio->relationLoaded('user') && $portfolio->user
                ? $this->formatUser($portfolio->user)
                : null,
        ];
    }

    private function formatUser(?User $user): ?array
    {
        if (!$user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'gender' => $user->gender ?? null,
            'role' => $user->role ?? 'user',
            'avatar' => $user->avatar ?? null,
            'avatar_url' => $this->resolveFileUrl($user->avatar),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }

    private function resolveFileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $diskPath = Storage::disk('public')->exists($path)
            ? $path
            : (Storage::disk('public')->exists('avatars/'.$path) ? 'avatars/'.$path : (Storage::disk('public')->exists('portfolios/'.$path) ? 'portfolios/'.$path : null));

        return $diskPath ? asset('storage/'.$diskPath) : null;
    }
}
