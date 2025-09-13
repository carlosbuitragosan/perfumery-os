<x-app-layout>
   <x-slot name="header">
      <h2>{{ $material->name }}</h2>
   </x-slot>

   <div class="p-4 space-y-4">
      <a
         href="{{ route('materials.bottles.create', $material) }}"
         class="inline-block bg-gray-800 text-white px-3 py-2 rounded-md hover:bg-gray-700"
      >
         ADD
      </a>
      {{-- Succes update message --}}
      @if (session('ok'))
         <div>{{ session('ok') }}</div>
      @endif

      {{-- Stock --}}
      <div class="card p-4">
         <div class="text-sm text-gray-400">No bottles yet.</div>
      </div>
   </div>
</x-app-layout>
