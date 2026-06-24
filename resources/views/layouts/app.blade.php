<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portiva</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body style="font-family: 'Poppins', sans-serif; background: linear-gradient(135deg,#07111f,#102a43); color:#eff6ff;">
    <!-- Global fixed header container (we'll move page header here) -->
    <div id="globalHeader" class="fixed top-0 left-0 right-0 z-50"></div>

    <div class="min-h-screen flex flex-col">
        <main id="mainContent" class="flex-grow">
            <!-- Flash notifications -->
            <div id="flashContainer" class="fixed right-6 top-24 z-60 space-y-3">
              @if(session('success'))
                <div class="px-4 py-3 rounded-lg bg-emerald-500 text-white shadow">{{ session('success') }}</div>
              @endif
              @if(session('error'))
                <div class="px-4 py-3 rounded-lg bg-red-500 text-white shadow">{{ session('error') }}</div>
              @endif
              @if($errors->any())
                <div class="px-4 py-3 rounded-lg bg-rose-500 text-white shadow">
                  <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)
                      <li>{{ $err }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
            </div>

            @yield('content')
        </main>

        <footer class="mt-12 py-6 text-center text-slate-300 text-sm border-t border-white/5 bg-gradient-to-t from-black/20">
            <div class="max-w-4xl mx-auto px-4">
                <div class="flex items-center justify-center">
                    <div>© {{ date('Y') }} Portiva. Kelompok 1.</div>
                </div>
            </div>
        </footer>
    </div>

      <script src="{{ asset('js/portiva.js') }}" defer></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const main = document.getElementById('mainContent');
        const globalHeader = document.getElementById('globalHeader');

        // Find the first header element inside the main content
        const pageHeader = main.querySelector('header') || document.querySelector('header');
        if (pageHeader) {
          // Move header into the fixed container
          globalHeader.appendChild(pageHeader);
          // Make sure header spans full width and has background
          pageHeader.classList.add('w-full');
          // Increase header padding/size for better visibility
          pageHeader.classList.add('py-4', 'md:py-6', 'px-6');
          // Ensure header has a backdrop so content underneath isn't visible
          pageHeader.classList.add('bg-black/40', 'backdrop-blur-sm', 'shadow-lg');
        }

        function adjustPadding() {
          const h = globalHeader.offsetHeight || 0;
          main.style.paddingTop = h + 'px';
        }

        // Adjust padding initially and on resize
        adjustPadding();
        window.addEventListener('resize', adjustPadding);
        // Auto-hide flash messages after 4s
        setTimeout(() => {
          const flash = document.getElementById('flashContainer');
          if (flash) flash.style.display = 'none';
        }, 4000);
      });
    </script>
</body>
</html>
