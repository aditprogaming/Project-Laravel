@extends('layouts.app')

@section('content')
<div class="px-6 py-6 text-white">
  <header class="flex items-center justify-between mb-8">
    <div class="text-3xl font-extrabold tracking-wide">portiva</div>
    <nav class="flex items-center gap-3 text-sm">
      <a href="/beranda" class="px-4 py-2 rounded-full border border-white/10">Beranda</a>
      <a href="/logout" class="px-4 py-2 rounded-full border border-rose-400 text-rose-100">Logout</a>
      <button id="settingsBtn" class="px-4 py-2 rounded-full border border-white/10 hover:bg-white/10 transition">☰</button>
    </nav>
  </header>

  <!-- Settings Popup Modal -->
  <div id="settingsModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-900 border border-white/20 rounded-2xl p-6 w-96 shadow-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white">Pengaturan</h2>
        <button id="closeModal" class="text-white/50 hover:text-white text-2xl">✕</button>
      </div>
      
      <nav class="space-y-3">
        <a href="/akun/ganti-nama" class="block px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 text-white transition">
          <div class="font-semibold">Ganti Nama</div>
          <div class="text-xs text-slate-400">Ubah nama pengguna Anda</div>
        </a>
        
        <a href="/akun/ganti-email" class="block px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 text-white transition">
          <div class="font-semibold">Ganti Email</div>
          <div class="text-xs text-slate-400">Ubah alamat email Anda</div>
        </a>
        
        <a href="/akun/ganti-password" class="block px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 text-white transition">
          <div class="font-semibold">Ganti Password</div>
          <div class="text-xs text-slate-400">Ubah kata sandi Anda</div>
        </a>
        <button id="deleteAccountBtn" class="w-full text-left block px-4 py-3 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-300 transition border border-red-500/30">
          <div class="font-semibold">Hapus Akun</div>
          <div class="text-xs text-red-400">Hapus akun Anda secara permanen</div>
        </button>
      </nav>
    </div>
  </div>

  <section class="rounded-3xl bg-white/10 border border-white/10 p-8 shadow-2xl">
    <div class="flex items-center gap-5">
      <div class="relative group cursor-pointer" id="avatarContainer">
        @php
          $avatarUrl = null;
          if(!empty($user->avatar)) {
            $a = $user->avatar;
            // Try different paths
            $paths = [
              $a,
              \Illuminate\Support\Str::contains($a, 'avatars/') ? $a : 'avatars/'.$a,
              \Illuminate\Support\Str::contains($a, 'portfolios/') ? $a : 'portfolios/'.$a,
            ];
            foreach ($paths as $path) {
              if(\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                $avatarUrl = asset('storage/'.$path);
                break;
              }
            }
            if (!$avatarUrl && filter_var($a, FILTER_VALIDATE_URL)) {
              $avatarUrl = $a;
            }
          }
        @endphp
        @if($avatarUrl)
          <img src="{{ $avatarUrl }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover" />
        @else
          <div class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-300 to-blue-500"></div>
        @endif
        <div class="absolute inset-0 w-20 h-20 rounded-full bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
          <span class="text-white text-xs font-semibold">Ubah Foto</span>
        </div>
      </div>
      <div>
        <h1 class="text-3xl font-black">{{ $user->name ?? 'Akun Portiva' }}</h1>
        <p class="text-slate-200">{{ $user->email ?? '-' }}</p>
      </div>
    </div>
  </section>

  <!-- Modal Upload Foto Akun -->
  <div id="uploadPhotoModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-900 border border-white/20 rounded-2xl p-6 w-96 shadow-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white">Ubah Foto Akun</h2>
        <button id="closeUploadModal" class="text-white/50 hover:text-white text-2xl">✕</button>
      </div>
      
      <form method="POST" action="{{ route('portiva.upload') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-semibold mb-2 text-white">Pilih Foto</label>
          <input type="file" name="photo" accept="image/*" required class="w-full rounded-2xl bg-slate-800 border border-white/10 p-3 text-slate-300 file:mr-4 file:px-3 file:py-2 file:rounded-full file:border-0 file:bg-cyan-400 file:text-slate-900 file:font-semibold file:cursor-pointer hover:file:bg-cyan-300">
          <p class="text-xs text-slate-400 mt-2">Format: JPG, PNG, atau GIF. Ukuran maksimal: 2MB</p>
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" id="cancelUploadBtn" class="px-4 py-2 rounded-full border border-white/10">Batal</button>
          <button type="submit" class="px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Hapus Akun -->
  <div id="deleteAccountModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-900 border border-white/20 rounded-2xl p-6 w-96 shadow-2xl">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-white">Konfirmasi Hapus Akun</h2>
        <button id="closeDeleteModal" class="text-white/50 hover:text-white text-2xl">✕</button>
      </div>
      <p class="text-slate-300 mb-6">Yakin ingin menghapus akun Anda? Tindakan ini akan menghapus semua portofolio dan data akun secara permanen dan tidak dapat dikembalikan.</p>

      <form method="POST" action="{{ route('portiva.account.delete') }}" class="flex justify-end gap-3">
        @csrf
        @method('DELETE')
        <button type="button" id="cancelDeleteBtn" class="px-4 py-2 rounded-full border border-white/10">Batal</button>
        <button type="submit" class="px-4 py-2 rounded-full bg-red-500 text-white font-semibold">Yakin Hapus</button>
      </form>
    </div>
  </div>

  @if(Session::has('admin'))
  <section class="mt-8 rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl">
    <h2 class="text-2xl font-bold">Daftar Pengguna Portiva</h2>
    <p class="text-slate-200 mt-2">Admin dapat melihat semua akun yang sudah terdaftar.</p>
    <div class="mt-6 grid md:grid-cols-2 gap-4">
      @foreach($users as $item)
        <article class="rounded-3xl bg-slate-900/60 border border-white/10 p-5">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-cyan-300 to-blue-500"></div>
            <div>
              <h3 class="text-lg font-semibold">{{ $item->name }}</h3>
              <p class="text-slate-300 text-sm">{{ $item->email }}</p>
            </div>
          </div>
          <p class="text-slate-200 text-sm mt-3">Role: {{ ucfirst($item->role ?? 'user') }}</p>
          <p class="text-slate-400 text-xs mt-1">Terdaftar: {{ $item->created_at->format('d M Y') }}</p>
          @if($item->id !== auth()->id())
            <form method="POST" action="{{ route('portiva.account.delete.admin', $item->id) }}" class="mt-4">
              @csrf
              @method('DELETE')
              <button type="submit" class="rounded-full border border-rose-400 px-3 py-2 text-sm text-rose-100 hover:bg-rose-500/10">Hapus Akun</button>
            </form>
          @endif
        </article>
      @endforeach
    </div>
  </section>
  @else
  <section class="mt-8 space-y-6">
    @foreach($profiles as $item)
      @if($item->template == 1)
      <!-- Template 1: Photo left, content right -->
      <article class="rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl">
        <div class="grid md:grid-cols-[200px_1fr] gap-6">
          <div class="flex flex-col items-center">
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
              <img src="{{ $photoUrl }}" alt="Foto {{ $item->name }}" class="w-40 h-40 object-cover rounded-2xl" />
            @else
              <div class="w-40 h-40 rounded-2xl bg-gradient-to-br from-cyan-300 to-blue-500"></div>
            @endif
          </div>
          <div>
            <h3 class="text-3xl font-semibold">{{ $item->name }}</h3>
            <p class="text-lg text-cyan-300 mt-1">{{ $item->profession }}</p>
            
            <div class="mt-6">
              <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Tentang Saya</h4>
              <p class="text-slate-200 text-sm mt-2">{{ $item->about ?? '-' }}</p>
            </div>

            @if($item->skills)
            <div class="mt-4">
              <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Skill</h4>
              <div class="flex flex-wrap gap-2 mt-2">
                @foreach(explode(',', $item->skills) as $skill)
                  <span class="inline-block px-3 py-1 rounded-full bg-cyan-400/20 text-cyan-300 text-xs">{{ trim($skill) }}</span>
                @endforeach
              </div>
            </div>
            @endif

            @if($item->experience)
            <div class="mt-4">
              <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Pengalaman</h4>
              <p class="text-slate-200 text-sm mt-2">{{ $item->experience }}</p>
            </div>
            @endif

            @if($item->contact)
            <div class="mt-4">
              <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Kontak</h4>
              <p class="text-slate-200 text-sm mt-2">{{ $item->contact }}</p>
            </div>
            @endif

            <div class="mt-6 flex gap-3">
              <a href="{{ route('portiva.view', $item->id) }}" class="inline-flex px-4 py-2 rounded-full border border-white/10">Lihat</a>
              <a href="/portofolio?template={{ $item->template }}" class="inline-flex px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Edit</a>
              <form
                method="POST"
                action="{{ route('portiva.delete', $item->id) }}"
                data-portiva-api-delete="true"
                data-portiva-api-url="{{ url('/api/portiva/profiles/'.$item->id) }}"
                data-confirm-message="Hapus portfolio ini?"
              >
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex px-4 py-2 rounded-full border border-red-500 text-red-300 hover:bg-red-500/10">Hapus</button>
              </form>
            </div>
          </div>
        </div>
      </article>

      @elseif($item->template == 2)
      <!-- Template 2: Photo centered, content below -->
      <article class="rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl">
        <div class="flex flex-col items-center text-center">
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
            <img src="{{ $photoUrl }}" alt="Foto {{ $item->name }}" class="w-40 h-40 object-cover rounded-2xl" />
          @else
            <div class="w-40 h-40 rounded-2xl bg-gradient-to-br from-cyan-300 to-blue-500"></div>
          @endif

          <h3 class="text-3xl font-semibold mt-6">{{ $item->name }}</h3>
          <p class="text-lg text-cyan-300 mt-1">{{ $item->profession }}</p>

          <div class="mt-6 w-full max-w-xl">
            <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Tentang Saya</h4>
            <p class="text-slate-200 text-sm mt-2">{{ $item->about ?? '-' }}</p>
          </div>

          @if($item->skills)
          <div class="mt-4 w-full max-w-xl">
            <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Skill</h4>
            <div class="flex flex-wrap gap-2 mt-2 justify-center">
              @foreach(explode(',', $item->skills) as $skill)
                <span class="inline-block px-3 py-1 rounded-full bg-cyan-400/20 text-cyan-300 text-xs">{{ trim($skill) }}</span>
              @endforeach
            </div>
          </div>
          @endif

          @if($item->experience)
          <div class="mt-4 w-full max-w-xl">
            <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Pengalaman</h4>
            <p class="text-slate-200 text-sm mt-2">{{ $item->experience }}</p>
          </div>
          @endif

          @if($item->contact)
          <div class="mt-4 w-full max-w-xl">
            <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Kontak</h4>
            <p class="text-slate-200 text-sm mt-2">{{ $item->contact }}</p>
          </div>
          @endif

          <div class="mt-6 flex gap-3 justify-center">
            <a href="{{ route('portiva.view', $item->id) }}" class="inline-flex px-4 py-2 rounded-full border border-white/10">Lihat</a>
            <a href="/portofolio?template={{ $item->template }}" class="inline-flex px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Edit</a>
            <form
              method="POST"
              action="{{ route('portiva.delete', $item->id) }}"
              data-portiva-api-delete="true"
              data-portiva-api-url="{{ url('/api/portiva/profiles/'.$item->id) }}"
              data-confirm-message="Hapus portfolio ini?"
            >
              @csrf
              @method('DELETE')
              <button type="submit" class="inline-flex px-4 py-2 rounded-full border border-red-500 text-red-300 hover:bg-red-500/10">Hapus</button>
            </form>
          </div>
        </div>
      </article>

      @else
      <!-- Template 3: Photo left, name/profession right -->
      <article class="rounded-3xl bg-white/10 border border-white/10 p-6 shadow-xl">
        <div class="grid md:grid-cols-[200px_1fr] gap-6 mb-6">
          <div class="flex flex-col items-center">
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
              <img src="{{ $photoUrl }}" alt="Foto {{ $item->name }}" class="w-40 h-40 object-cover rounded-2xl" />
            @else
              <div class="w-40 h-40 rounded-2xl bg-gradient-to-br from-cyan-300 to-blue-500"></div>
            @endif
          </div>
          <div>
            <h3 class="text-3xl font-semibold">{{ $item->name }}</h3>
            <p class="text-lg text-cyan-300 mt-1">{{ $item->profession }}</p>
          </div>
        </div>

        <div class="border-t border-white/10 pt-6">
          <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Tentang Saya</h4>
          <p class="text-slate-200 text-sm mt-2">{{ $item->about ?? '-' }}</p>

          @if($item->skills)
          <div class="mt-4">
            <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Skill</h4>
            <div class="flex flex-wrap gap-2 mt-2">
              @foreach(explode(',', $item->skills) as $skill)
                <span class="inline-block px-3 py-1 rounded-full bg-cyan-400/20 text-cyan-300 text-xs">{{ trim($skill) }}</span>
              @endforeach
            </div>
          </div>
          @endif

          @if($item->experience)
          <div class="mt-4">
            <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Pengalaman</h4>
            <p class="text-slate-200 text-sm mt-2">{{ $item->experience }}</p>
          </div>
          @endif

          @if($item->contact)
          <div class="mt-4">
            <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide">Kontak</h4>
            <p class="text-slate-200 text-sm mt-2">{{ $item->contact }}</p>
          </div>
          @endif
        </div>

        <div class="mt-6 flex gap-3">
          <a href="{{ route('portiva.view', $item->id) }}" class="inline-flex px-4 py-2 rounded-full border border-white/10">Lihat</a>
          <a href="/portofolio?template={{ $item->template }}" class="inline-flex px-4 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold">Edit</a>
          <form
            method="POST"
            action="{{ route('portiva.delete', $item->id) }}"
            data-portiva-api-delete="true"
            data-portiva-api-url="{{ url('/api/portiva/profiles/'.$item->id) }}"
            data-confirm-message="Hapus portfolio ini?"
          >
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex px-4 py-2 rounded-full border border-red-500 text-red-300 hover:bg-red-500/10">Hapus</button>
          </form>
        </div>
      </article>
      @endif
    @endforeach
  </section>
  @endif
</div>

<script>
  const settingsBtn = document.getElementById('settingsBtn');
  const settingsModal = document.getElementById('settingsModal');
  const closeModal = document.getElementById('closeModal');

  // Buka modal saat tombol hamburger diklik
  settingsBtn.addEventListener('click', () => {
    settingsModal.classList.remove('hidden');
  });

  // Tutup modal saat tombol X diklik
  closeModal.addEventListener('click', () => {
    settingsModal.classList.add('hidden');
  });

  // Tutup modal saat klik area gelap (luar modal)
  settingsModal.addEventListener('click', (e) => {
    if (e.target === settingsModal) {
      settingsModal.classList.add('hidden');
    }
  });

  // Handle upload foto akun modal
  const avatarContainer = document.getElementById('avatarContainer');
  const uploadPhotoModal = document.getElementById('uploadPhotoModal');
  const closeUploadModal = document.getElementById('closeUploadModal');
  const cancelUploadBtn = document.getElementById('cancelUploadBtn');

  avatarContainer.addEventListener('click', () => {
    uploadPhotoModal.classList.remove('hidden');
  });

  closeUploadModal.addEventListener('click', () => {
    uploadPhotoModal.classList.add('hidden');
  });

  cancelUploadBtn.addEventListener('click', () => {
    uploadPhotoModal.classList.add('hidden');
  });

  uploadPhotoModal.addEventListener('click', (e) => {
    if (e.target === uploadPhotoModal) {
      uploadPhotoModal.classList.add('hidden');
    }
  });

  // Handle delete account modal
  const deleteAccountBtn = document.getElementById('deleteAccountBtn');
  const deleteAccountModal = document.getElementById('deleteAccountModal');
  const closeDeleteModal = document.getElementById('closeDeleteModal');
  const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

  if (deleteAccountBtn) {
    deleteAccountBtn.addEventListener('click', () => {
      settingsModal.classList.add('hidden');
      deleteAccountModal.classList.remove('hidden');
    });
  }

  if (closeDeleteModal) {
    closeDeleteModal.addEventListener('click', () => {
      deleteAccountModal.classList.add('hidden');
    });
  }

  if (cancelDeleteBtn) {
    cancelDeleteBtn.addEventListener('click', () => {
      deleteAccountModal.classList.add('hidden');
    });
  }

  deleteAccountModal.addEventListener('click', (e) => {
    if (e.target === deleteAccountModal) {
      deleteAccountModal.classList.add('hidden');
    }
  });
</script>
@endsection
