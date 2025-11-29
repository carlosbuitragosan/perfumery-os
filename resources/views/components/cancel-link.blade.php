<a
   {{
      $attributes->merge([
         'class' => 'inline-flex items-center px-4 py-2 rounded-md  font-semibold text-xs uppercase tracking-widest text-white bg-red-600 hover:bg-red-700 active:bg-red-800 border border-transparent focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition ease-in-out duration-150',
      ])
   }}
>
   {{ $slot }}
</a>
