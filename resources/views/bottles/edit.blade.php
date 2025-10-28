<x-app-layout>
   <x-slot name="header">
      <h2>Edit Bottle</h2>
      <span class="text-sm">{{ $bottle->material->name }}</span>
   </x-slot>
   <div class="p-4 space-y-4">
      <form method="POST" action="" enctype="multipart/form-data" class="space-y-3">
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
            <span class="text-sm">Distillation Date</span>
            <input
               type="date"
               name="distillation_date"
               class="p-2 w-full"
               value="{{ old('distillation_date', $bottle->distillation_date) }}"
            />
         </label>

         <label class="block">
            <span class="text-sm">Purchase Date</span>
            <input
               type="date"
               name="purchase_date"
               class="p-2 w-full"
               value="{{ old('purchase_date', $bottle->purchase_date) }}"
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

         <label class="block">
            <input
               type="file"
               name="files[]"
               multiple
               class="pb-2 w-full text-sm file:mr-4 file:rounded-md file:px-4 file:py-1 file:text-sm file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer"
            />
         </label>

         <div class="flex gap-2">
            <x-primary-button type="submit" class="bg-green-600 hover:bg-green-700">
               SAVE
            </x-primary-button>
            <a href="" class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-sm font-semibold">
               CANCEL
            </a>
         </div>
      </form>
   </div>
</x-app-layout>
