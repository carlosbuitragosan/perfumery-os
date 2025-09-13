<x-app-layout>
   <x-slot name="header">
      <h2>Create Bottle</h2>
      <span class="text-sm">{{ $material->name }}</span>
   </x-slot>
   <div class="p-4 space-y-4">
      <form action="#" method="POST" enctype="multipart/form-data" class="space-y-3">
         @csrf

         <label class="block">
            <span class="text-sm">Supplier name</span>
            <input type="text" name="supplier_name" class="p-2 w-full" />
         </label>

         <label class="block">
            <span class="text-sm">Supplier URL</span>
            <input type="url" name="supplier_url" class="p-2 w-full" />
         </label>

         <label class="block">
            <span class="text-sm">Batch code</span>
            <input type="text" name="batch_code" class="p-2 w-full" />
         </label>

         <label class="block">
            <span class="text-sm">Method</span>
            <select name="method" class="p-2 w-full"></select>
         </label>

         <label class="block">
            <span class="text-sm">Plant part</span>
            <input type="text" name="plant_part" class="p-2 w-full" />
         </label>

         <label class="block">
            <span class="text-sm">Origin country</span>
            <input type="text" name="origin_country" class="p-2 w-full" />
         </label>

         <label class="block">
            <span class="text-sm">Distillation Date</span>
            <input type="date" name="distillation_date" class="p-2 w-full" />
         </label>

         <label class="block">
            <span class="text-sm">Purchase Date</span>
            <input type="date" name="purchase_date" class="p-2 w-full" />
         </label>

         <label class="block">
            <span class="text-sm">Volume (ml)</span>
            <input type="number" name="volume_ml" class="p-2 w-full" />
         </label>

         <label class="block">
            <span class="text-sm">Price</span>
            <input type="number" name="price" class="p-2 w-full" />
         </label>

         <label class="block">
            <span class="text-sm">Notes</span>
            <textarea name="notes" rows="4" class="p-2 w-full"></textarea>
         </label>

         <label class="block">
            <span class="text-sm">Files</span>
            <input
               type="file"
               name="files[]"
               multiple
               class="pb-2 w-full text-sm file:mr-4 file:rounded-md file:px-4 file:py-1 file:text-sm file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer"
            />
         </label>

         <x-primary-button type="submit" class="bg-green-600 hover:bg-green-700">
            SAVE
         </x-primary-button>
      </form>
   </div>
</x-app-layout>
