@extends('layouts.app')

@section('content')
<div class="px-6 py-6 text-white">
  <header class="flex items-center justify-between mb-10">
    <div class="text-3xl font-extrabold tracking-wide">portiva</div>
    <nav class="flex gap-3 text-sm">
      <a href="/beranda" class="px-4 py-2 rounded-full border border-white/10">Beranda</a>
      <a href="/template" class="px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Template</a>
      <a href="/akun" class="px-4 py-2 rounded-full border border-white/10">Akun</a>
      <a href="/logout" class="px-4 py-2 rounded-full border border-rose-400 text-rose-100">Logout</a>
    </nav>
  </header>

  <section class="rounded-3xl bg-white/10 border border-white/10 p-8 shadow-2xl">
    <h1 class="text-4xl font-black">Template</h1>
    <p class="mt-3 text-slate-200">Mau buat apa hari ini?</p>
  </section>

  <section class="mt-8 grid md:grid-cols-3 gap-6">
    @for($i=1; $i<=3; $i++)
      <article class="rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl">
        <div class="h-32 rounded-3xl bg-gradient-to-br from-cyan-400/30 to-blue-500/40"></div>
        <h3 class="mt-4 text-xl font-semibold">Model {{ $i }}</h3>
        <p class="text-slate-200 mt-2">Template modern dengan layout yang dapat Anda sesuaikan untuk portofolio Anda.</p>
        <div class="mt-4 flex gap-3">
          <a href="/portofolio?template={{ $i }}" class="px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Gunakan Template Sekarang</a>
          @if(Session::has('admin'))
            <button class="px-4 py-2 rounded-full border border-rose-400 text-rose-100">Hapus</button>
          @endif
        </div>
      </article>
    @endfor
  </section>

  <section class="mt-10 rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl">
    <h2 class="text-2xl font-bold">Highlights</h2>
    <ul class="grid md:grid-cols-3 gap-4 mt-4 text-slate-100">
      <li class="rounded-2xl bg-slate-900/40 p-4">Tampilan modern dan responsif</li>
      <li class="rounded-2xl bg-slate-900/40 p-4">Mudah dibagikan ke orang lain</li>
      <li class="rounded-2xl bg-slate-900/40 p-4">Dapat diupdate kapan saja</li>
    </ul>
  </section>
</div>
@endsection
