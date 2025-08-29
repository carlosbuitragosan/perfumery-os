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
                <a href="{{ route('materials.edit', $m) }}"
                    class="block px-3 py-3 hover:bg-gray-800 focus:bg-gray-800 focus:outline-none">
                    <div class="font-medium text-gray-100">{{ $m->name }}</div>
                    <div class="text-sm text-gray-400">
                        @if ($m->category)
                            {{ $m->category }}
                        @endif
                        @if ($m->category && $m->botanical)
                            ·
                        @endif
                        @if ($m->botanical)
                            {{ $m->botanical }}
                        @endif
                    </div>
                </a>
            @empty
                <div class="p-3 text-gray-400">No materials yet.</div>
            @endforelse
        </div>

        <div class="text-gray-400">{{ $materials->links() }}</div>
    </div>
</x-app-layout>
