<div class="space-y-3">
   <div class="relative" x-data="{ q: $wire.entangle('query') }">
      <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
         @include('icons.search')
      </span>
      <input
         x-model="q"
         x-ref="search"
         class="w-full pl-10 pr-8"
         type="search"
         name="query"
         inputmode="search"
         wire:model.live="query"
         autocomplete="off"
         spellcheck="false"
         enterkeyhint="search"
      />

      @if (filled($query))
         <button
            x-show="q !== ''"
            type="button"
            @click="q=''; $wire.resetPage()"
            aria-label="Clear search"
            class="absolute inset-y-0 right-0 pr-3 opacity-80 hover:opacity-100"
         >
            <span class="text-xl">&times;</span>
         </button>
      @endif
   </div>

   {{-- Button to create material --}}
   <a
      href="{{ route('materials.create') }}"
      class="inline-block bg-gray-800 text-white px-3 py-2 rounded-md hover:bg-gray-700"
   >
      ADD
   </a>

   {{-- Show message when material has been added --}}
   @if (session('ok'))
      <div class="rounded bg-green-900/50 text-green-200 px-3 py-2">
         {{ session('ok') }}
      </div>
   @endif

   {{-- Materials list --}}
   <div class="divide-y divide-gray-800 card">
      @forelse ($materials as $m)
         <div
            id="material-{{ $m->id }}"
            x-data
            @click="window.location='{{ route('materials.show', $m) }}'"
            class="px-3 py-3 hover:bg-gray-800 focus:bg-gray-800 focus:outline-none cursor-pointer rounded-md"
         >
            {{-- Name links to material edit page --}}
            <a
               href="{{ route('materials.edit', $m) }}"
               class="inline-block bg-gray-700 hover:bg-gray-900 rounded px-2 py-1 mb-2"
            >
               <div class="font-medium text-gray-100">
                  {{ $m->name }}
               </div>
               <div class="text-xs mb-1">
                  @if ($m->botanical)
                     {{ $m->botanical }}
                  @endif
               </div>
            </a>

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
               if (! is_null($m->ifra_max_pct)) {
                  $chips[] = [
                     'label' => 'IFRA4 ' . rtrim(rtrim(number_format((float) $m->ifra_max_pct, 2, '.', ''), '0'), '.') . '%',
                     'class' => $colors['ifra'],
                  ];
               }
            @endphp

            <div class="flex flex-wrap gap-2 justify-end">
               @foreach ($chips as $chip)
                  <span class="px-2 py-0.5 text-xs rounded border {{ $chip['class'] }}">
                     {{ $chip['label'] }}
                  </span>
               @endforeach
            </div>
            <div class="text-sm text-gray-400 mt-2">
               @if ($m->notes)
                  {{ $m->notes }}
               @endif
            </div>
         </div>
      @empty
         <div class="p-3 text-gray-400">No materials found.</div>
      @endforelse
   </div>
</div>
