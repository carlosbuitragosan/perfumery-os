<button
   {{
      $attributes->merge([
         'type' => 'submit',
         'class' => 'inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-sky-800 text-white hover:bg-sky-700 active:bg-sky-900 focus:ring-offset-2 focus:ring-offset-white dark:bg-sky-700 dark:text-gray-100 dark:hover:bg-sky-600 dark:active:bg-sky-800 dark:focus:ring-offset-gray-900',
      ])
   }}
>
   {{ $slot }}
</button>
