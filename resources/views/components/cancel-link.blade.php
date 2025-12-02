<a
   {{
      $attributes->merge([
         'class' => 'inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest text-slate-900 bg-gray-200 hover:bg-gray-300 active:bg-gray-400 dark:text-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:active:bg-gray-500 border border-transparent focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition ease-in-out duration-150',
      ])
   }}
>
   {{ $slot }}
</a>
