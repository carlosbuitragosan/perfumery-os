@props([
   'active',
])

@php
   $classes =
      $active ?? false
         ? 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out border-indigo-500 text-slate-900 dark:border-indigo-400 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900'
         : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out text-slate-600 hover:text-slate-900 hover:border-slate-300 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
   {{ $slot }}
</a>
