<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Materials</h2>
    </x-slot>

    <div class="p-4 space-y-4">
        <a href="{{ route('materials.create') }}"
            class="inline-block bg-gray-900  px-3 py-2 rounded-md hover:bg-gray-800">ADD</a>
        @if (session('ok'))
            <div class="text-green-700 mb-2">{{ session('ok') }}</div>
        @endif

        @forelse($materials as $m)
            <div class="py-2 border-b">{{ $m->name }}</div>
        @empty
            <div class="text-gray-600">No materials yet.</div>
        @endforelse

        <div class="mt-3">
            {{ $materials->links() }}
        </div>
    </div>
</x-app-layout>
