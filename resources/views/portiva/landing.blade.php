@extends('layouts.app')

@section('content')
<div class="px-6 py-6 text-white">
  <header class="flex items-center justify-between mb-10">
    <div class="text-3xl font-extrabold tracking-wide">portiva</div>
    <nav class="flex gap-3">
      <button onclick="openLogin()" class="px-4 py-2 rounded-full border border-cyan-400 text-cyan-100">Login</button>
      <button onclick="openRegister()" class="px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Buat Akun</button>
    </nav>
  </header>

  <section class="grid lg:grid-cols-[1.1fr_0.9fr] gap-10 items-start">
    <div>
      <p class="text-cyan-200 uppercase tracking-[0.35em] text-sm">portofolio digital</p>
      <h1 class="text-5xl font-black mt-3">Portofolio Viva</h1>
      <p class="mt-6 text-slate-200 leading-7">Portiva adalah website portofolio digital yang dirancang untuk membantu pengguna menyimpan, mengelola, dan menampilkan dokumen, karya, prestasi, serta hasil pekerjaan dalam satu format yang terorganisir.</p>
      <p class="mt-4 text-slate-200 leading-7">Portiva memungkinkan pengguna menunjukkan kemampuan, pengalaman, dan pencapaian secara profesional melalui tampilan yang mudah diakses dan dibagikan.</p>
    </div>

    <aside class="rounded-3xl bg-white/10 border border-white/10 p-6 shadow-2xl">
      <h2 class="text-2xl font-bold">Kenapa Portiva? Karena:</h2>
      <ul class="mt-4 space-y-3 text-slate-100">
        <li>• Tampilan rapi dan profesional</li>
        <li>• Mudah mengelola karya dan pengalaman</li>
        <li>• Bisa dibagikan ke recruiter atau HRD</li>
      </ul>
    </aside>
  </section>

  <section class="mt-10 grid grid-cols-1 lg:grid-cols-5 gap-4">
    <div class="rounded-3xl border border-white/10 bg-white/8 p-5 shadow-xl">Tampilan rapi dan profesional</div>
    <div class="rounded-3xl border border-white/10 bg-white/8 p-5 shadow-xl">Mudah mengelola karya dan pengalaman</div>
    <div class="rounded-3xl border border-white/10 bg-white/8 p-5 shadow-xl">Dapat dibagikan ke recruiter atau HRD</div>
    <div class="rounded-3xl border border-white/10 bg-white/8 p-5 shadow-xl">Template yang dapat disesuaikan</div>
    <div class="rounded-3xl border border-white/10 bg-white/8 p-5 shadow-xl">Upload foto & file pendukung</div>
  </section>

  <section class="mt-12 text-center">
    <h2 class="text-3xl font-bold">Tim Pembuat</h2>
    <div class="grid md:grid-cols-3 gap-6 mt-6">
      @php
        $teamPhotos = ['acep.jpeg', 'adit.jpeg', 'puyu.jpeg'];
        $teamNames = ['Acep', 'Adit', 'Puyu'];
      @endphp
      @foreach($teamPhotos as $index => $photo)
      <article class="rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl flex flex-col items-center text-center">
        <img src="{{ asset('img/' . $photo) }}" alt="{{ $teamNames[$index] }}" class="w-40 h-40 rounded-2xl object-cover border border-white/10">
        <h3 class="mt-6 text-xl font-semibold">{{ $teamNames[$index] }}</h3>
        <p class="text-slate-200 mt-3">Anggota tim yang mengembangkan Portiva untuk pengalaman portofolio digital yang modern.</p>
        <button onclick="openAdmin()" class="mt-6 px-6 py-2 rounded-full bg-emerald-400 text-slate-900 font-semibold">Masuk</button>
      </article>
      @endforeach
    </div>
  </section>

  <section class="mt-12 rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl">
    <h2 class="text-2xl font-bold">Highlights</h2>
    <ul class="grid md:grid-cols-3 gap-4 mt-4 text-slate-100">
      <li class="rounded-2xl bg-slate-900/40 p-4">Tampilan modern dan responsif</li>
      <li class="rounded-2xl bg-slate-900/40 p-4">Mudah dibagikan ke orang lain</li>
      <li class="rounded-2xl bg-slate-900/40 p-4">Dapat diupdate kapan saja</li>
    </ul>
  </section>

</div>

<div id="registerModal" class="hidden fixed inset-0 bg-black/60 items-center justify-center p-4">
  <div class="bg-slate-900 rounded-3xl p-6 w-full max-w-xl border border-white/10 shadow-2xl">
    <h3 class="text-2xl font-bold">Buat Akun Portiva</h3>
    <p class="text-slate-300 mt-2">Buat akun Portiva Anda sekarang.</p>
    <form method="POST" action="{{ route('portiva.register') }}" class="mt-6 space-y-4">
      @csrf
      <input name="name" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" placeholder="Nama lengkap" required>
      <input name="email" type="email" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" placeholder="Email / Nomor ponsel" required>
      <input name="phone" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" placeholder="Nomor ponsel">
      <select name="gender" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3">
        <option value="Laki-laki">Laki-laki</option>
        <option value="Perempuan">Perempuan</option>
        <option value="Lainnya">Lainnya</option>
      </select>
      <input name="password" type="password" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" placeholder="Password" required>
      <input name="password_confirmation" type="password" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" placeholder="Konfirmasi password" required>
      <label class="flex items-center gap-2 text-sm text-slate-200"><input type="checkbox" required> Saya setuju</label>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="closeModal('registerModal')" class="px-4 py-2 rounded-full border border-white/10">Batal</button>
        <button type="submit" class="px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Buat Akun</button>
      </div>
    </form>
  </div>
</div>

<div id="loginModal" class="hidden fixed inset-0 bg-black/60 items-center justify-center p-4">
  <div class="bg-slate-900 rounded-3xl p-6 w-full max-w-md border border-white/10 shadow-2xl">
    <h3 class="text-2xl font-bold">Login ke Akun Portiva</h3>
    <form method="POST" action="{{ route('portiva.login') }}" class="mt-6 space-y-4">
      @csrf
      <input name="email" type="email" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" placeholder="Email" required>
      <input name="password" type="password" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" placeholder="Password" required>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="closeModal('loginModal')" class="px-4 py-2 rounded-full border border-white/10">Batal</button>
        <button type="submit" class="px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Login</button>
      </div>
    </form>
  </div>
</div>

<div id="adminModal" class="hidden fixed inset-0 bg-black/60 items-center justify-center p-4">
  <div class="bg-slate-900 rounded-3xl p-6 w-full max-w-md border border-white/10 shadow-2xl">
    <h3 class="text-2xl font-bold">Masuk Sebagai Admin</h3>
    <form method="POST" action="{{ route('portiva.admin.login') }}" class="mt-6 space-y-4">
      @csrf
      <input name="code" class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3" placeholder="Masukkan kode" required>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="closeModal('adminModal')" class="px-4 py-2 rounded-full border border-white/10">Batal</button>
        <button type="submit" class="px-4 py-2 rounded-full bg-emerald-400 text-slate-900 font-semibold">Masuk</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openRegister(){ document.getElementById('registerModal').classList.remove('hidden'); document.getElementById('registerModal').classList.add('flex'); }
  function openLogin(){ document.getElementById('loginModal').classList.remove('hidden'); document.getElementById('loginModal').classList.add('flex'); }
  function openAdmin(){ document.getElementById('adminModal').classList.remove('hidden'); document.getElementById('adminModal').classList.add('flex'); }
  function closeModal(id){ document.getElementById(id).classList.add('hidden'); document.getElementById(id).classList.remove('flex'); }
</script>
@endsection
