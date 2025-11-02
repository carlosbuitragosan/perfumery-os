@php
   use Illuminate\Support\Carbon;
   use Illuminate\Support\Str;
   use App\Enums\ExtractionMethod;
@endphp

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
         <div class="rounded bg-green-900/50 text-green-200 px-3 py-2">{{ session('ok') }}</div>
      @endif

      {{-- Stock --}}
      <div class="flex flex-col gap-2">
         @forelse ($material->bottles as $bottle)
            @php
               $enum = ExtractionMethod::tryFrom((string) $bottle->method);
            @endphp

            <div class="rounded border p-4 text-sm space-y-1" id="bottle-{{ $bottle->id }}">
               <div class="flex items-center gap-2 mb-1">
                  @if ($bottle->is_active)
                     <span
                        class="text-sm px-2 py-0.5 rounded bg-green-100 text-green-700 font-medium"
                     >
                        In use
                     </span>
                  @else
                     <span
                        class="text-sm px-2 py-0.5 rounded bg-green-100 text-green-700 font-medium"
                     >
                        Finished
                     </span>
                  @endif
               </div>

               @if (filled($bottle->supplier_name))
                  <div>
                     <span class="font-medium">Supplier:</span>
                     {{ $bottle->supplier_name }}
                  </div>
               @endif

               @if ($bottle->supplier_url)
                  <div class="flex gap-1 whitespace-nowrap">
                     <span class="font-medium">URL:</span>
                     <a
                        href="{{ $bottle->supplier_url }}"
                        title="{{ $bottle->supplier_url }}"
                        class="underline inline-block max-w-full truncate align-top"
                     >
                        {{ $bottle->supplier_url }}
                     </a>
                  </div>
               @endif

               @if (filled($bottle->batch_code))
                  <div>
                     <span class="font-medium">Batch:</span>
                     {{ $bottle->batch_code }}
                  </div>
               @endif

               @if (filled($bottle->method))
                  <div>
                     <span class="font-medium">Method:</span>
                     {{ $enum?->label() }}
                  </div>
               @endif

               @if (filled($bottle->plant_part))
                  <div>
                     <span class="font-medium">Plant part:</span>
                     {{ $bottle->plant_part }}
                  </div>
               @endif

               @if (filled($bottle->origin_country))
                  <div>
                     <span class="font-medium">Origin:</span>
                     {{ $bottle->origin_country }}
                  </div>
               @endif

               @if ($bottle->purchase_date)
                  <div>
                     <span class="font-medium">Purchase date:</span>
                     {{ Carbon::parse($bottle->purchase_date)->format('d/m/Y') }}
                  </div>
               @endif

               @if ($bottle->expiry_date)
                  <div>
                     <span class="font-medium">Expiry date:</span>
                     {{ Carbon::parse($bottle->expiry_date)->format('d/m/Y') }}
                  </div>
               @endif

               @if (filled($bottle->volume_ml))
                  <div>
                     <span class="font-medium">Volume:</span>
                     {{ rtrim(rtrim(number_format((float) $bottle->volume_ml, 2, '.', ''), '0'), '.') }}
                     ml
                  </div>
               @endif

               @if (filled($bottle->density))
                  <div>
                     <span class="font-medium">Density:</span>
                     {{ number_format((float) $bottle->density, 3) }} g/ml
                  </div>
               @endif

               @if (filled($bottle->price))
                  <div>
                     <span class="font-medium">Price:</span>
                     £{{ number_format((float) $bottle->price, 2) }}
                  </div>
               @endif

               @if (filled($bottle->notes))
                  <div>
                     <span class="font-medium">Notes:</span>
                     {{ $bottle->notes }}
                  </div>
               @endif

               <div class="flex justify-between items-end">
                  @if ($bottle->is_active)
                     <form method="POST" action="{{ route('bottles.finish', $bottle) }}">
                        @csrf
                        <button
                           type="submit"
                           class="bg-transparent text-red-600 underline text-xs font-semibold"
                        >
                           MARK AS FINISHED
                        </button>
                     </form>
                  @endif

                  <div class="flex gap-2 justify-end">
                     <x-link
                        href="{{ route('bottles.edit', $bottle) }} "
                        class="text-sm bg-green-600 hover:bg-green-700"
                     >
                        EDIT
                     </x-link>
                  </div>
               </div>
            </div>
         @empty
            <div class="card p-4">
               <div class="text-sm text-gray-400">No bottles yet.</div>
            </div>
         @endforelse
      </div>
   </div>
</x-app-layout>
