<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark h-full">
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

      {{-- Favicons --}}
      <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
      <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
      <link rel="shortcut icon" href="/favicon/favicon.ico" />
      <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
      <meta name="apple-mobile-web-app-title" content="Naturals" />
      <link rel="manifest" href="/favicon/site.webmanifest" />
      <meta name="apple-mobile-web-app-capable" content="yes" />
      <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

      <!-- Scripts -->
      @vite(['resources/css/app.css', 'resources/js/app.js'])
      @livewireStyles
   </head>

   <body class="font-sans antialiased bg-gray-950 text-gray-100">
      <div class="min-h-screen">
         @include('layouts.navigation')

         <!-- Page Heading -->
         @isset($header)
            <header class="bg-gray-900 border-b border-gray-800">
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
