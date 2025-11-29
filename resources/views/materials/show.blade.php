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
         class="inline-flex items-center px-3 py-2 rounded-md text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900"
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

            <div class="card border p-4 text-sm space-y-1" id="bottle-{{ $bottle->id }}">
               <div class="flex items-center gap-2 mb-1">
                  @if ($bottle->is_active)
                     <span
                        class="text-sm px-2 py-0.5 rounded font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-100"
                     >
                        In use
                     </span>
                  @else
                     <span
                        class="text-sm px-2 py-0.5 rounded font-medium bg-slate-200 text-slate-800 dark:bg-slate-800 dark:text-slate-100"
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
                        class="inline-block max-w-full truncate align-top underline text-indigo-600 hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 rounded-sm"
                        target="_blank"
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

               @if ($bottle->files->isNotEmpty())
                  <div class="mt-2 flex flex-wrap gap-2 items-center">
                     <span>Files:</span>
                     @foreach ($bottle->files as $file)
                        <a
                           href="{{ Storage::disk('public')->url($file->path) }}"
                           class="bottle-file-link inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs border border-gray-300 text-slate-900 bg-white hover:border-indigo-500 hover:text-indigo-600 dark:border-gray-700 dark:text-gray-100 dark:bg-gray-900 dark:hover:border-indigo-500 dark:hover:text-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900"
                           target="_blank"
                        >
                           {{ $file->original_name }}
                        </a>
                     @endforeach
                  </div>
               @endif

               <div class="flex justify-between items-end">
                  <div>
                     @if ($bottle->is_active)
                        <form method="POST" action="{{ route('bottles.finish', $bottle) }}">
                           @csrf
                           <button
                              type="submit"
                              class="px-2 py-1 text-xs font-semibold underline rounded text-rose-700 hover:text-rose-600 dark:text-rose-300 dark:hover:text-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900"
                           >
                              MARK AS FINISHED
                           </button>
                        </form>
                     @endif
                  </div>

                  <div class="flex gap-2 justify-end">
                     <form
                        method="POST"
                        action="{{ route('bottles.destroy', $bottle) }}"
                        class="bottle-delete-form"
                        onsubmit="return confirm('Are you sure you want to delete this bottle?')"
                     >
                        @csrf
                        @method('DELETE')
                        <x-danger-button type="submit">DELETE</x-danger-button>
                     </form>
                     <x-link
                        href="{{ route('bottles.edit', $bottle) }} "
                        class="text-sm bg-green-600 hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 text-white"
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
