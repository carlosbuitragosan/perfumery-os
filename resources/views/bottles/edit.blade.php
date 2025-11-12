<x-app-layout>
   <x-slot name="header">
      <h2>Edit Bottle</h2>
      <span class="text-sm">{{ $bottle->material->name }}</span>
   </x-slot>
   <div class="p-4 space-y-4">
      <form
         id="bottle-edit-form"
         method="POST"
         action="{{ route('bottles.update', $bottle) }}"
         enctype="multipart/form-data"
         class="space-y-3"
      >
         @csrf
         @method('PATCH')

         <label class="block">
            <span class="text-sm">Supplier name</span>
            <input
               type="text"
               name="supplier_name"
               class="p-2 w-full"
               value="{{ old('suplier_name', $bottle->supplier_name) }}"
            />
         </label>

         <label class="block">
            <span class="text-sm">Supplier URL</span>
            <input
               type="url"
               name="supplier_url"
               class="p-2 w-full"
               value="{{ old('supplier_url', $bottle->supplier_url) }}"
            />
         </label>

         <label class="block">
            <span class="text-sm">Batch code</span>
            <input
               type="text"
               name="batch_code"
               class="p-2 w-full"
               value="{{ old('batch_code', $bottle->batch_code) }}"
            />
         </label>

         <label class="block">
            <span class="text-sm">Method</span>
            <select name="method" class="p-2 w-full" required>
               <option value="" disabled {{ old('method', $bottle->method) ? '' : 'selected' }}>
                  Chose method...
               </option>
               @foreach (\App\Enums\ExtractionMethod::cases() as $m)
                  <option
                     value="{{ $m->value }}"
                     @selected(old('method', $bottle->method) === $m->value)
                  >
                     {{ $m->label() }}
                  </option>
               @endforeach
            </select>
         </label>

         <label class="block">
            <span class="text-sm">Plant part</span>
            <input
               type="text"
               name="plant_part"
               class="p-2 w-full"
               value="{{ old('plant_part', $bottle->plant_part) }}"
            />
         </label>

         <label class="block">
            <span class="text-sm">Origin country</span>
            <input
               type="text"
               name="origin_country"
               class="p-2 w-full"
               value="{{ old('origin_country', $bottle->origin_country) }}"
            />
         </label>

         <label class="block">
            <span class="text-sm">Purchase date</span>
            <input
               type="date"
               name="purchase_date"
               class="p-2 w-full"
               value="{{ old('purchase_date', $bottle->purchase_date) }}"
            />
         </label>

         <label class="block">
            <span class="text-sm">Expiry date</span>
            <input
               type="date"
               name="expiry_date"
               class="p-2 w-full"
               value="{{ old('expiry_date', $bottle->expiry_date) }}"
            />
         </label>

         <label class="block">
            <span class="text-sm">Volume (ml)</span>
            <input
               type="number"
               name="volume_ml"
               inputmode="numeric"
               class="p-2 w-full"
               value="{{ old('volume_ml', $bottle->volume_ml) }}"
            />
         </label>

         <label class="block">
            <span class="text-sm">Density (g/ml)</span>
            <input
               type="number"
               name="density"
               inputmode="decimal"
               step="0.001"
               class="p-2 w-full"
               value="{{ old('density', $bottle->density) }}"
            />
         </label>

         <label class="block">
            <span class="text-sm">Price</span>
            <input
               type="number"
               name="price"
               inputmode="decimal"
               step="0.01"
               class="p-2 w-full"
               value="{{ old('price', $bottle->price) }}"
            />
         </label>

         <label class="block">
            <span class="text-sm">Notes</span>
            <textarea name="notes" rows="4" class="p-2 w-full">
{{ old('notes', $bottle->notes) }}</textarea
            >
         </label>

         {{-- Alpine.js to enable multiple uploads --}}
         <div x-data="{ count: 1, firstSelected: false }" class="space-y-2 pb-4">
            <template x-for="i in count" :key="i">
               <input
                  type="file"
                  name="files[]"
                  multiple
                  class="pb-2 w-full text-sm file:mr-4 file:rounded-md file:px-4 file:py-1 file:text-sm file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer"
                  @change="firstSelected = true"
               />
            </template>
            <button
               class="text-xs text-indigo-400 hover:text-indigo-300 underline"
               type="button"
               @click="count++"
               x-show="firstSelected"
            >
               ADD ANOTHER FILE
            </button>
         </div>

         @if ($bottle->files->isNotEmpty())
            <div class="mt-2 text-sm">
               <div class="mb-4">
                  <span class="block font-semibold mb-1">Attached files</span>
                  <span class="italic mb-6">Select to remove when saving</span>
               </div>

               @foreach ($bottle->files as $file)
                  <div class="inline-flex items-center gap-2 px-4 mb-6">
                     <input
                        type="checkbox"
                        name="remove_files[]"
                        value="{{ $file->id }}"
                        class="rounded bg-gray-500 focus:ring-indigo-900"
                     />
                     <a
                        href="{{ Storage::disk('public')->url($file->path) }}"
                        target="_blank"
                        class="hover:text-indigo-300"
                     >
                        {{ $file->original_name }}
                     </a>
                  </div>
               @endforeach
            </div>
         @endif

         <div class="flex gap-2">
            <x-primary-button type="submit" class="bg-green-600 hover:bg-green-700">
               SAVE
            </x-primary-button>
            <a
               href="{{ route('materials.show', $bottle->material) }}#bottle-{{ $bottle->id }}"
               class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-sm font-semibold"
            >
               CANCEL
            </a>
         </div>
      </form>
   </div>
</x-app-layout>
