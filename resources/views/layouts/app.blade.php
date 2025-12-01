<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="csrf-token" content="{{ csrf_token() }}" />

      <title>{{ config('app.name', 'Laravel') }}</title>

      <!-- Fonts -->
      <link rel="preconnect" href="https://fonts.bunny.net" />
      <link
         href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
         rel="stylesheet"
      />

      @include('layouts.partials.favicons')

      {{-- dark theme bootstrap --}}
      <script>
         (function () {
            const STORAGE_KEY = 'theme';
            const root = document.documentElement;

            try {
               const stored = localStorage.getItem(STORAGE_KEY);
               const prefersDark =
                  window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

               const theme = stored || (prefersDark ? 'dark' : 'light');

               if (theme === 'dark') {
                  root.classList.add('dark');
                  root.dataset.theme = 'dark';
               } else {
                  root.classList.remove('dark');
                  root.dataset.theme = 'light';
               }
            } catch (e) {
               root.classList.remove('dark');
               root.dataset.theme = 'light';
            }
         })();
      </script>

      <!-- Scripts -->
      @vite(['resources/css/app.css', 'resources/js/app.js'])
      @livewireStyles
   </head>

   <body class="font-sans antialiased bg-white text-slate-900 dark:bg-gray-950 dark:text-gray-100">
      <div class="min-h-screen">
         @include('layouts.navigation')

         <!-- Page Heading -->
         @isset($header)
            <header class="bg-white border-b border-gray-200 dark:bg-gray-900 dark:border-gray-800">
               <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                  {{ $header }}
               </div>
            </header>
         @endisset

         <!-- Page Content -->
         <main>
            {{ $slot }}
         </main>
      </div>
      @livewireScripts
   </body>
</html>
