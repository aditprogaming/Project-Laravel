@extends('layouts.app')

@section('content')
<div class="px-6 py-6 text-white">
  <header class="flex items-center justify-between mb-10">
    <div class="text-3xl font-extrabold tracking-wide">portiva</div>
    <nav class="flex gap-3 text-sm">
      <a href="/beranda" class="px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Beranda</a>
      <a href="/template" class="px-4 py-2 rounded-full border border-white/10">Template</a>
      <a href="/akun" class="px-4 py-2 rounded-full border border-white/10">Akun</a>
      <a href="/logout" class="px-4 py-2 rounded-full border border-rose-400 text-rose-100">Logout</a>
    </nav>
  </header>

  <section class="rounded-3xl bg-white/10 border border-white/10 p-8 shadow-2xl">
    <p class="uppercase tracking-[0.35em] text-cyan-200 text-xs">welcome</p>
    <h1 class="text-4xl font-black mt-3">Welcome to Portiva</h1>
    <p class="mt-4 text-slate-200 max-w-3xl">Berikut portofolio yang sudah dibuat oleh orang lain. Anda dapat melihat profil yang tersimpan dan mengelolanya melalui menu akun.</p>
  </section>

  <section class="mt-8 grid md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($profiles as $item)
      <article class="rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl">
        <div class="flex items-center justify-between gap-4">
          @php
            $photoUrl = null;
            if($item->photo) {
              $p = $item->photo;
              $photoPath = \Illuminate\Support\Str::contains($p, 'portfolios/') ? $p : 'portfolios/'.$p;
              if(\Illuminate\Support\Facades\Storage::disk('public')->exists($photoPath)) {
                $photoUrl = asset('storage/'.$photoPath);
              } elseif(filter_var($p, FILTER_VALIDATE_URL)) {
                $photoUrl = $p;
              }
            }
          @endphp
          @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="Foto {{ $item->name }}" class="w-20 h-20 rounded-full object-cover flex-shrink-0" />
          @else
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-300 to-blue-500 flex-shrink-0"></div>
          @endif
          <div class="flex-1">
            <h3 class="text-xl font-semibold">{{ $item->name }}</h3>
            <p class="text-slate-300 text-sm">{{ $item->user->email ?? '-' }}</p>
          </div>
          <a href="{{ route('portiva.view', $item->id) }}" class="px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold whitespace-nowrap flex-shrink-0">Lihat</a>
        </div>
      </article>
    @endforeach
  </section>
</div>
@endsection
