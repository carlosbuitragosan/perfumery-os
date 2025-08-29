<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Materials</h2>
    </x-slot>

    <div class="p-4 space-y-4">
        <a href="{{ route('materials.create') }}"
            class="inline-block bg-gray-800 text-white px-3 py-2 rounded-md hover:bg-gray-700">
            ADD
        </a>

        @if (session('ok'))
            <div class="rounded bg-green-900/50 text-green-200 px-3 py-2">{{ session('ok') }}</div>
        @endif

        <div class="divide-y divide-gray-800 card">
            @forelse($materials as $m)
                <div class="py-3 px-3">
                    <div class="font-medium text-gray-100">{{ $m->name }}</div>
                    <div class="text-sm text-gray-400">
                        {{ $m->category ?? '—' }} · {{ $m->botanical ?? '—' }}
                    </div>
                </div>
            @empty
                <div class="p-3 text-gray-400">No materials yet.</div>
            @endforelse
        </div>

        <div class="text-gray-400">{{ $materials->links() }}</div>
    </div>
</x-app-layout>
