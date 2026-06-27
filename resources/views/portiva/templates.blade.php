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

  @if(Session::has('admin'))
    <section class="mt-8 rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl">
      <form action="{{ route('portiva.template.store') }}" method="POST" class="flex flex-col gap-3 md:flex-row md:items-center">
        @csrf
        <input type="text" name="name" placeholder="Nama template baru" class="flex-1 rounded-full border border-white/10 bg-slate-950/40 px-4 py-3 text-sm text-white outline-none focus:border-cyan-400" required>
        <button type="submit" class="rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-900">Tambah Template</button>
      </form>
    </section>
  @endif

  <section class="mt-8 grid md:grid-cols-3 gap-6">
    @foreach($templates as $template)
      <article class="rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl">
        <div class="h-32 rounded-3xl bg-gradient-to-br from-cyan-400/30 to-blue-500/40"></div>
        <h3 class="mt-4 text-xl font-semibold">{{ $template['name'] ?? 'Template ' . $template['id'] }}</h3>
        <p class="text-slate-200 mt-2">Template modern dengan layout yang dapat Anda sesuaikan untuk portofolio Anda.</p>
        <div class="mt-4 flex flex-wrap gap-3">
          <a href="/portofolio?template={{ $template['id'] }}" class="px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Gunakan</a>
          @if(Session::has('admin'))
            <form action="{{ route('portiva.template.update', $template['id']) }}" method="POST" class="flex gap-2">
              @csrf
              @method('PATCH')
              <input type="text" name="name" value="{{ $template['name'] ?? 'Template ' . $template['id'] }}" class="w-32 rounded-full border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-white outline-none focus:border-cyan-400">
              <button type="submit" class="rounded-full border border-cyan-400 px-3 py-2 text-sm text-cyan-200">Edit</button>
            </form>
            <form action="{{ route('portiva.template.destroy', $template['id']) }}" method="POST" class="flex">
              @csrf
              @method('DELETE')
              <button type="submit" class="rounded-full border border-rose-400 px-3 py-2 text-sm text-rose-100">Hapus</button>
            </form>
          @endif
        </div>
      </article>
    @endforeach
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
