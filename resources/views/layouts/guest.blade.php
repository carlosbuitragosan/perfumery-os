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
   </head>

   <body
      class="font-sans antialiased bg-gray-50 text-slate-900 dark:bg-gray-950 dark:text-gray-100"
   >
      <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
         <div>
            <a href="/" class="card-focus inline-block rounded p-1">
               <img
                  src="{{ asset('images/fragrance.png') }}"
                  alt="Natuals Perfumery"
                  class="w-20 h-20 fill-current"
               />
            </a>
         </div>

         <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white text-slate-900 border border-gray-200 shadow-md sm:rounded-lg dark:bg-gray-900 dark:text-gray-100 dark:border-gray-800"
         >
            {{ $slot }}
         </div>
      </div>
   </body>
</html>
