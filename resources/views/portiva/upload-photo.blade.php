@extends('layouts.app')

@section('content')
<div class="px-6 py-6 text-white">
  <header class="flex items-center justify-between mb-8">
    <div class="text-3xl font-extrabold tracking-wide">portiva</div>
    <nav class="flex gap-3 text-sm">
      <a href="/beranda" class="px-4 py-2 rounded-full border border-white/10">Beranda</a>
      <a href="/template" class="px-4 py-2 rounded-full border border-white/10">Template</a>
      <a href="/akun" class="px-4 py-2 rounded-full border border-white/10">Akun</a>
      <a href="/logout" class="px-4 py-2 rounded-full border border-rose-400 text-rose-100">Logout</a>
    </nav>
  </header>

  <section class="rounded-3xl bg-white/10 border border-white/10 p-8 shadow-2xl">
    <h1 class="text-4xl font-black">Unggah Foto Akun</h1>
    <p class="mt-3 text-slate-200">Pilih foto untuk menampilkan di profil akun Anda.</p>
  </section>

  <section class="mt-8 max-w-2xl mx-auto rounded-3xl bg-white/10 border border-white/10 p-8 shadow-2xl">
    <form method="POST" action="{{ route('portiva.upload') }}" enctype="multipart/form-data" class="space-y-6">
      @csrf

      <div class="flex flex-col items-center">
        @php
          $avatarUrl = null;
          if(!empty($user->avatar)) {
            $a = $user->avatar;
            $avatarPath = \Illuminate\Support\Str::contains($a, 'avatars/') ? $a : 'avatars/'.$a;
            if(\Illuminate\Support\Facades\Storage::disk('public')->exists($avatarPath)) {
              $avatarUrl = asset('storage/'.$avatarPath);
            } elseif(filter_var($a, FILTER_VALIDATE_URL)) {
              $avatarUrl = $a;
            }
          }
        @endphp
        @if($avatarUrl)
          <img src="{{ $avatarUrl }}" alt="Foto Akun" class="w-32 h-32 rounded-full object-cover mb-4" />
        @else
          <div class="w-32 h-32 rounded-full bg-gradient-to-br from-cyan-300 to-blue-500 mb-4"></div>
        @endif
        <p class="text-slate-200 text-sm">Foto akun terkini</p>
      </div>

      <div>
        <label class="block text-sm font-semibold mb-2">Pilih Foto Baru</label>
        <input type="file" name="photo" accept="image/*" required class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3 text-slate-300 file:mr-4 file:px-3 file:py-2 file:rounded-full file:border-0 file:bg-cyan-400 file:text-slate-900 file:font-semibold file:cursor-pointer hover:file:bg-cyan-300">
        <p class="text-xs text-slate-400 mt-2">Format: JPG, PNG, atau GIF. Ukuran maksimal: 2MB</p>
      </div>

      <div class="flex justify-end gap-3">
        <a href="/akun" class="px-6 py-3 rounded-full border border-white/10">Batal</a>
        <button type="submit" class="px-6 py-3 rounded-full bg-cyan-400 text-slate-900 font-semibold">Simpan Foto</button>
      </div>
    </form>
  </section>
</div>
@endsection
