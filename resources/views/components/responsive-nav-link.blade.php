@props(['active' => false])

@php
   $baseClasses =
      'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2';

   $classes = $active
      ? $baseClasses .
         'focus:ring-offset-white dark:focus:ring-offset-gray-950 border-indigo-500 text-indigo-700 bg-indigo-50 dark:border-indigo-400 dark:text-indigo-200 dark:bg-gray-800 focus:bg-indigo-50 dark:focus:bg-gray-800'
      : $baseClasses .
         'focus:ring-offset-white dark:focus:ring-offset-gray-950 border-transparent text-slate-700 hover:text-slate-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800 focus:bg-gray-100 dark:focus:bg-gray-800';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
   {{ $slot }}
</a>
