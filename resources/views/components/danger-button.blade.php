<button
   {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 text-white border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest bg-red-600 hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900/70 transition ease-in-out duration-150']) }}
>
   {{ $slot }}
</button>
