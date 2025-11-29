<x-app-layout>
   <x-slot name="header">
      <h2 class="font-semibold text-xl text-slate-900 dark:text-gray-100 leading-tight">
         {{ __('Dashboard') }}
      </h2>
   </x-slot>

   <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
         <div
            class="bg-white border border-gray-200 shadow-sm sm:rounded-lg dark:bg-gray-900 dark:border-gray-800 overflow-hidden"
         >
            <div class="card p-6">
               {{ __("You're logged in!") }}
            </div>
         </div>
      </div>
   </div>
</x-app-layout>
