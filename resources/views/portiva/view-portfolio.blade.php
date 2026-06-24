@extends('layouts.app')

@section('content')
<div class="px-6 py-6 text-white">
  <header class="flex items-center justify-between mb-8">
    <div class="text-3xl font-extrabold tracking-wide">portiva</div>
    <nav class="flex gap-3 text-sm">
      <a href="/beranda" class="px-4 py-2 rounded-full border border-white/10">Beranda</a>
      <a href="/akun" class="px-4 py-2 rounded-full border border-white/10">Akun</a>
      @if(Session::has('user'))
        <a href="/logout" class="px-4 py-2 rounded-full border border-rose-400 text-rose-100">Logout</a>
      @else
        <button onclick="openLogin()" class="px-4 py-2 rounded-full border border-cyan-400 text-cyan-100">Login</button>
      @endif
    </nav>
  </header>

  @if($portfolio->template == 1)
  <!-- Template 1: Photo left, content right -->
  <section class="rounded-3xl bg-white/10 border border-white/10 p-8 shadow-2xl">
    <div class="grid md:grid-cols-[250px_1fr] gap-8">
      <div class="flex flex-col items-center">
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
          <img src="{{ $photoPath }}" alt="{{ $portfolio->name }}" class="w-48 h-48 rounded-2xl object-cover">
        @else
          <div class="w-48 h-48 rounded-2xl bg-gradient-to-br from-cyan-300 to-blue-500"></div>
        @endif
      </div>
      <div>
        <h1 class="text-4xl font-bold">{{ $portfolio->name }}</h1>
        <p class="text-2xl text-cyan-300 mt-2">{{ $portfolio->profession }}</p>
        
        <div class="mt-8">
          <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Tentang Saya</h3>
          <p class="text-slate-200 text-base mt-3 leading-relaxed">{{ $portfolio->about }}</p>
        </div>

        @if($portfolio->skills)
        <div class="mt-6">
          <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Skill</h3>
          <div class="flex flex-wrap gap-3 mt-3">
            @foreach(explode(',', $portfolio->skills) as $skill)
              <span class="inline-block px-4 py-2 rounded-full bg-cyan-400/20 text-cyan-300 text-sm border border-cyan-400/50">{{ trim($skill) }}</span>
            @endforeach
          </div>
        </div>
        @endif

        @if($portfolio->experience)
        <div class="mt-6">
          <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Pengalaman</h3>
          <p class="text-slate-200 text-base mt-3 leading-relaxed">{{ $portfolio->experience }}</p>
        </div>
        @endif

        @if($portfolio->contact)
        <div class="mt-6">
          <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Kontak</h3>
          <p class="text-slate-200 text-base mt-3 leading-relaxed">{{ $portfolio->contact }}</p>
        </div>
        @endif
      </div>
    </div>
  </section>

  @elseif($portfolio->template == 2)
  <!-- Template 2: Photo centered, content below -->
  <section class="rounded-3xl bg-white/10 border border-white/10 p-8 shadow-2xl">
    <div class="flex flex-col items-center text-center max-w-3xl mx-auto">
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
        <img src="{{ $photoPath }}" alt="{{ $portfolio->name }}" class="w-48 h-48 rounded-2xl object-cover">
      @else
        <div class="w-48 h-48 rounded-2xl bg-gradient-to-br from-cyan-300 to-blue-500"></div>
      @endif
      
      <h1 class="text-4xl font-bold mt-8">{{ $portfolio->name }}</h1>
      <p class="text-2xl text-cyan-300 mt-2">{{ $portfolio->profession }}</p>

      <div class="mt-8">
        <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Tentang Saya</h3>
        <p class="text-slate-200 text-base mt-3 leading-relaxed">{{ $portfolio->about }}</p>
      </div>

      @if($portfolio->skills)
      <div class="mt-6">
        <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Skill</h3>
        <div class="flex flex-wrap gap-3 mt-3 justify-center">
          @foreach(explode(',', $portfolio->skills) as $skill)
            <span class="inline-block px-4 py-2 rounded-full bg-cyan-400/20 text-cyan-300 text-sm border border-cyan-400/50">{{ trim($skill) }}</span>
          @endforeach
        </div>
      </div>
      @endif

      @if($portfolio->experience)
      <div class="mt-6">
        <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Pengalaman</h3>
        <p class="text-slate-200 text-base mt-3 leading-relaxed">{{ $portfolio->experience }}</p>
      </div>
      @endif

      @if($portfolio->contact)
      <div class="mt-6">
        <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Kontak</h3>
        <p class="text-slate-200 text-base mt-3 leading-relaxed">{{ $portfolio->contact }}</p>
      </div>
      @endif
    </div>
  </section>

  @else
  <!-- Template 3: Photo left, name/profession right -->
  <section class="rounded-3xl bg-white/10 border border-white/10 p-8 shadow-2xl">
    <div class="grid md:grid-cols-[250px_1fr] gap-8 pb-8 border-b border-white/10">
      <div class="flex flex-col items-center">
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
          <img src="{{ $photoPath }}" alt="{{ $portfolio->name }}" class="w-48 h-48 rounded-2xl object-cover">
        @else
          <div class="w-48 h-48 rounded-2xl bg-gradient-to-br from-cyan-300 to-blue-500"></div>
        @endif
      </div>
      <div>
        <h1 class="text-4xl font-bold">{{ $portfolio->name }}</h1>
        <p class="text-2xl text-cyan-300 mt-2">{{ $portfolio->profession }}</p>
      </div>
    </div>

    <div class="pt-8">
      <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Tentang Saya</h3>
      <p class="text-slate-200 text-base mt-3 leading-relaxed">{{ $portfolio->about }}</p>

      @if($portfolio->skills)
      <div class="mt-6">
        <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Skill</h3>
        <div class="flex flex-wrap gap-3 mt-3">
          @foreach(explode(',', $portfolio->skills) as $skill)
            <span class="inline-block px-4 py-2 rounded-full bg-cyan-400/20 text-cyan-300 text-sm border border-cyan-400/50">{{ trim($skill) }}</span>
          @endforeach
        </div>
      </div>
      @endif

      @if($portfolio->experience)
      <div class="mt-6">
        <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Pengalaman</h3>
        <p class="text-slate-200 text-base mt-3 leading-relaxed">{{ $portfolio->experience }}</p>
      </div>
      @endif

      @if($portfolio->contact)
      <div class="mt-6">
        <h3 class="text-lg font-bold text-slate-300 uppercase tracking-wider">Kontak</h3>
        <p class="text-slate-200 text-base mt-3 leading-relaxed">{{ $portfolio->contact }}</p>
      </div>
      @endif
    </div>
  </section>
  @endif
</div>
@endsection
