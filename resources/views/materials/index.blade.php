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
                    <div>
                        <div class="font-medium text-gray-100">{{ $m->name }}</div>
                        <div class="text-sm text-gray-400 mb-1">
                            @if ($m->botanical)
                                {{ $m->botanical }}
                            @endif
                        </div>
                    </div>

                    {{-- Tags --}}
                    @php
                        $colors = [
                            'pyramid' => 'bg-indigo-900 border-indigo-700 text-indigo-100',
                            'families' => 'bg-green-900 border-green-700 text-green-100',
                            'functions' => 'bg-yellow-900 border-yellow-700 text-yellow-100',
                            'safety' => 'bg-red-900 border-red-700 text-red-100',
                            'effects' => 'bg-purple-900 border-purple-700 text-purple-100',
                            'ifra' => 'bg-blue-900 border-blue-700 text-blue-100',
                        ];

                        $chips = [];

                        // Pyramid
                        foreach ((array) $m->pyramid ?? [] as $p) {
                            $chips[] = ['label' => Str::ucfirst($p), 'class' => $colors['pyramid']];
                        }

                        // Families
                        foreach ((array) $m->families ?? [] as $p) {
                            $chips[] = ['label' => Str::ucfirst($p), 'class' => $colors['families']];
                        }

                        // Functions
                        foreach ((array) $m->functions ?? [] as $p) {
                            $chips[] = ['label' => Str::ucfirst($p), 'class' => $colors['functions']];
                        }

                        // Effects
                        foreach ((array) $m->effects ?? [] as $p) {
                            $chips[] = ['label' => Str::ucfirst($p), 'class' => $colors['effects']];
                        }

                        // Safety
                        foreach ((array) $m->safety ?? [] as $p) {
                            $chips[] = ['label' => Str::ucfirst($p), 'class' => $colors['safety']];
                        }

                        // IFRA max %
                        if (!is_null($m->ifra_max_pct)) {
                            $chips[] = [
                                'label' =>
                                    'IFRA4 ' .
                                    rtrim(rtrim(number_format((float) $m->ifra_max_pct, 2, '.', ''), '0'), '.') .
                                    '%',
                                'class' => $colors['ifra'],
                            ];
                        }
                    @endphp

                    <div class="flex flex-wrap gap-2 justify-end">
                        @foreach ($chips as $chip)
                            <span
                                class="px-2 py-0.5 text-xs rounded border {{ $chip['class'] }}">{{ $chip['label'] }}</span>
                        @endforeach
                    </div>
                    <div class="text-sm text-gray-400 mt-2">
                        @if ($m->notes)
                            {{ $m->notes }}
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
