<button
   {{
      $attributes->merge([
         'type' => 'submit',
         'class' => 'inline-flex items-center px-4 py-2  text-white border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest  active:bg-slate-800  dark:text-gray-100 dark:active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition ease-in-out duration-150',
      ])
   }}
>
   {{ $slot }}
</button>
