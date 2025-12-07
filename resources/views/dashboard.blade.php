<x-app-layout>
   <x-slot name="header">
      <h2 class="font-semibold text-xl text-slate-900 dark:text-gray-100 leading-tight">
         {{ __('Dashboard') }}
      </h2>
   </x-slot>

   <div class="p-6">
      <div>
         <x-link href="{{ route('blends.create') }}">Create Blend test</x-link>
      </div>
   </div>
</x-app-layout>
