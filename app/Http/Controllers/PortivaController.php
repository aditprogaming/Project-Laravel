<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PortivaController extends Controller
{
    public function landing()
    {
        $profiles = Schema::hasTable('portfolios')
            ? Portfolio::with('user')->latest()->take(3)->get()
            : collect();

        return view('portiva.landing', compact('profiles'));
    }

    public function dashboard()
    {
        if (!Session::has('user') && !Session::has('admin')) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        $profiles = Schema::hasTable('portfolios')
            ? Portfolio::with('user')->latest()->take(6)->get()
            : collect();

        return view('portiva.dashboard', compact('profiles'));
    }

    public function templates()
    {
        if (!Session::has('user') && !Session::has('admin')) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('portiva.templates');
    }

    public function portfolio(Request $request)
    {
        if (!Session::has('user') && !Session::has('admin')) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        $template = (int) ($request->query('template', 1));
        $portfolio = Schema::hasTable('portfolios')
            ? Portfolio::firstOrNew([
                'user_id' => Session::get('user'),
                'template' => $template,
            ])
            : new Portfolio(['template' => $template]);

        return view('portiva.portfolio', compact('template', 'portfolio'));
    }

    public function account()
    {
        if (!Session::has('user') && !Session::has('admin')) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Session::has('admin')
            ? (object) ['name' => 'Admin Portiva', 'email' => 'admin@portiva.test']
            : User::find(Session::get('user'));

        $profiles = Schema::hasTable('portfolios')
            ? (Session::has('admin')
                ? Portfolio::with('user')->latest()->get()
                : Portfolio::with('user')->where('user_id', $user?->id)->get())
            : collect();

        $users = User::orderBy('created_at', 'desc')->get();

        return view('portiva.account', compact('user', 'profiles', 'users'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? 'Lainnya',
            'password' => $data['password'],
            'role' => 'user',
        ]);

        Session::put('user', $user->id);

        return redirect('/beranda')->with('success', 'Akun berhasil dibuat.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Email atau password salah.');
        }

        Session::put('user', $user->id);
        Session::forget('admin');

        return redirect('/beranda')->with('success', 'Login berhasil.');
    }

    public function adminLogin(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        if ($request->code === 'Unfari2025') {
            Session::put('admin', true);
            Session::forget('user');

            return redirect('/beranda')->with('success', 'Anda masuk sebagai admin.');
        }

        return back()->with('error', 'Kode admin salah.');
    }

    public function savePortfolio(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'profession' => 'required|string|max:255',
            'about' => 'required|string',
            'skills' => 'required|string',
            'experience' => 'required|string',
            'contact' => 'required|string',
            'template' => 'nullable|integer',
            'photo' => 'nullable|image|max:2048',
        ]);

        $userId = Session::get('user');
        if (!$userId) {
            return back()->with('error', 'Login dibutuhkan untuk menyimpan portfolio.');
        }

        $template = $request->template ?? 1;
        $portfolio = Portfolio::where('user_id', $userId)->where('template', $template)->first();
        $photoPath = $portfolio?->photo;

        if ($request->hasFile('photo')) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('portfolios', 'public');
        }

        // Jika pengguna memilih menggunakan foto portfolio sebagai foto akun, simpan ke users.avatar
        if ($request->has('use_for_account') && $photoPath) {
            $user = User::find($userId);
            if ($user) {
                $user->avatar = $photoPath;
                $user->save();
            }
        }

        Portfolio::updateOrCreate(
            ['user_id' => $userId, 'template' => $template],
            [
                'name' => $request->name,
                'profession' => $request->profession,
                'about' => $request->about,
                'skills' => $request->skills,
                'experience' => $request->experience,
                'contact' => $request->contact,
                'template' => $template,
                'photo' => $photoPath,
            ]
        );

        // Redirect to next template
        $nextTemplate = $template + 1;
        return redirect("/portofolio?template=$nextTemplate")->with('success', 'Data portfolio berhasil disimpan.');
    }

    public function viewPortfolio($id)
    {
        $portfolio = Portfolio::findOrFail($id);

        return view('portiva.view-portfolio', compact('portfolio'));
    }

    public function deletePortfolio($id)
    {
        $portfolio = Portfolio::findOrFail($id);

        // only allow owner or admin to delete
        if (!Session::has('admin') && $portfolio->user_id != Session::get('user')) {
            return redirect('/akun')->with('error', 'Anda tidak memiliki izin untuk menghapus portfolio ini.');
        }

        $portfolio->delete();

        return redirect('/akun')->with('success', 'Portfolio berhasil dihapus.');
    }

    public function uploadPhotoForm()
    {
        if (!Session::has('user') && !Session::has('admin')) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = User::find(Session::get('user'));

        return view('portiva.upload-photo', compact('user'));
    }

    public function uploadPhoto(Request $request)
    {
        if (!Session::has('user')) {
            return back()->with('error', 'Login dibutuhkan.');
        }

        $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        $userId = Session::get('user');
        $user = User::find($userId);

        if (!$user) {
            return back()->with('error', 'Pengguna tidak ditemukan.');
        }

        // Hapus avatar lama jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Simpan foto baru
        $avatarPath = $request->file('photo')->store('avatars', 'public');

        // Update user avatar
        $user->avatar = $avatarPath;
        $user->save();

        return redirect('/akun')->with('success', 'Foto akun berhasil diperbarui.');
    }

    public function deleteAccount(Request $request)
    {
        $userId = Session::get('user');
        if (!$userId) {
            return redirect('/')->with('error', 'Anda harus login untuk menghapus akun.');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect('/')->with('error', 'Pengguna tidak ditemukan.');
        }

        // Hapus semua portfolio milik user beserta file fotonya
        $portfolios = Portfolio::where('user_id', $userId)->get();
        foreach ($portfolios as $p) {
            if (!empty($p->photo) && Storage::disk('public')->exists($p->photo)) {
                Storage::disk('public')->delete($p->photo);
            }
            $p->delete();
        }

        // Hapus avatar jika ada
        if (!empty($user->avatar) && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Hapus user
        $user->delete();

        // Logout
        Session::flush();

        return redirect('/')->with('success', 'Akun berhasil dihapus permanen.');
    }

    public function logout()
    {
        Session::flush();

        return redirect('/')->with('success', 'Anda berhasil keluar.');
    }
}
