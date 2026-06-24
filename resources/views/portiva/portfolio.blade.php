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
    <h1 class="text-4xl font-black">Halaman Portofolio</h1>
    <p class="mt-3 text-slate-200">Gunakan template sekarang untuk mengedit dan menyimpan profil Anda.</p>
  </section>

  <section class="mt-8 grid gap-8 lg:grid-cols-[1fr_1.3fr]">
    <aside class="rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl">
        <div class="w-32 h-32 rounded-3xl overflow-hidden bg-slate-800">
            @php
              $photoPath = null;
              if(!empty($portfolio->photo)) {
                  if(\Illuminate\Support\Facades\Storage::disk('public')->exists($portfolio->photo)) {
                      $photoPath = asset('storage/' . $portfolio->photo);
                  } elseif(\Illuminate\Support\Facades\Storage::disk('public')->exists('portfolios/' . $portfolio->photo)) {
                      $photoPath = asset('storage/portfolios/' . $portfolio->photo);
                  } elseif(\Illuminate\Support\Facades\Storage::disk('public')->exists('portfolios/' . basename($portfolio->photo))) {
                      $photoPath = asset('storage/portfolios/' . basename($portfolio->photo));
                  }
              }
            @endphp
            @if($photoPath)
              <img src="{{ $photoPath }}" alt="Foto Profil" class="w-full h-full object-cover">
            @else
              <div class="w-full h-full bg-gradient-to-br from-cyan-300 to-blue-500"></div>
            @endif
          </div>
      <p class="mt-4 text-slate-200 text-sm">{{ $portfolio->about ?? 'Tentang saya...' }}</p>
    </aside>

    <form
      method="POST"
      action="{{ route('portiva.save') }}"
      enctype="multipart/form-data"
      class="rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl space-y-4"
      data-portiva-api-form="portfolio"
      data-portiva-api-url="{{ $portfolio ? url('/api/portiva/profiles/'.$portfolio->id) : url('/api/portiva/profiles') }}"
      data-portiva-api-method="{{ $portfolio ? 'PUT' : 'POST' }}"
    >
      @csrf
      <input type="hidden" name="template" value="{{ $template }}">
      <div class="grid md:grid-cols-2 gap-4">
        <input name="name" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" value="{{ $portfolio->name ?? '' }}" placeholder="Nama" required>
        <input name="profession" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" value="{{ $portfolio->profession ?? '' }}" placeholder="Profesi" required>
      </div>
      <textarea name="about" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" rows="3" placeholder="Tentang saya">{{ $portfolio->about ?? '' }}</textarea>
      <textarea name="skills" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" rows="3" placeholder="Skill (pisahkan dengan koma)">{{ $portfolio->skills ?? '' }}</textarea>
      <textarea name="experience" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" rows="3" placeholder="Pengalaman">{{ $portfolio->experience ?? '' }}</textarea>
      <textarea name="contact" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" rows="3" placeholder="Kontak (wa, email, ig, tiktok)">{{ $portfolio->contact ?? '' }}</textarea>
      <div>
        <label class="block text-sm font-semibold mb-2">Upload Foto Profil</label>
        <input type="file" name="photo" accept="image/*" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3 text-slate-300 file:mr-4 file:px-3 file:py-2 file:rounded-full file:border-0 file:bg-cyan-400 file:text-slate-900 file:font-semibold file:cursor-pointer hover:file:bg-cyan-300">
        @if($portfolio->photo ?? null)
          <p class="text-xs text-slate-400 mt-2">Foto saat ini: <span class="text-cyan-400">{{ basename($portfolio->photo) }}</span></p>
        @endif
        <label class="inline-flex items-center gap-2 mt-3 text-sm">
          <input type="checkbox" name="use_for_account" class="w-4 h-4" />
          <span>Gunakan foto ini sebagai foto akun</span>
        </label>
      </div>
      <div class="flex justify-end gap-3">
        <a href="/template" class="px-4 py-2 rounded-full border border-white/10">Batal</a>
        <button type="submit" class="px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Simpan</button>
      </div>
    </form>
  </section>
</div>
@endsection
