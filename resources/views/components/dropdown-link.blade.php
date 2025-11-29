<a
   {{
      $attributes->merge([
         'class' => 'block w-full px-4 py-2 text-start text-sm leading-5 text-slate-900 hover:bg-gray-100 hover:text-slate-900 dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 focus:background-gray-100rounded transition duration-150 ease-in-out',
      ])
   }}
>
   {{ $slot }}
</a>
