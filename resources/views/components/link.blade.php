<a
   {{
      $attributes->merge([
         'class' => 'inline-flex items-center px-4 py-2 rounded-md border border-transparent font-semibold text-xs uppercase tracking-widest bg-gray-100 text-slate-900 hover:bg-gray-200 active:bg-gray-300 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700 dark:active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition ease-in-out duration-150',
      ])
   }}
>
   {{ $slot }}
</a>
