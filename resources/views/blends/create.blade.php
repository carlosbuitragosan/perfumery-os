<x-app-layout>
   <x-slot name="header">
      <h2 class="font-semibold text-xl">Create Blend</h2>
   </x-slot>

   <div class="p-4">
      <form
         id="create-blend-form"
         method="POST"
         action="{{ route('blends.store') }}"
         class="space-y-3"
      >
         @csrf
         <label class="block">
            <span class="text-sm">Blend Name</span>
            <input type="text" name="name" class="p-2 w-full" />
         </label>

         <h3 class="font-medium">Ingredients</h3>
         {{-- INGREDIENTS --}}
         <div class="space-y-6" data-testid="ingredients-container">
            <div class="flex flex-col gap-3" data-testid="ingredient-row" data-index="0">
               {{-- MATERIAL --}}
               <select name="materials[0][material_id]" class="w-full p-2">
                  <option value="">Select material</option>
                  @foreach ($materials as $material)
                     <option value="{{ $material->id }}">{{ $material->name }}</option>
                  @endforeach
               </select>
               <div class="flex gap-4">
                  {{-- DROPS --}}
                  <input
                     type="number"
                     name="materials[0][drops]"
                     placeholder="number of drops"
                     class="w-full p-2"
                  />
                  {{-- DILUTION --}}
                  <select name="materials[0][dilution]" class="w-full p-2">
                     <option value="25">25%</option>
                     <option value="10">10%</option>
                     <option value="1">1%</option>
                  </select>
               </div>
            </div>
         </div>

         <x-primary-button type="button" data-testid="add-ingredient" class="bg-indigo-600">
            Add ingredient
         </x-primary-button>

         {{-- template for dynamic rows --}}
         <template data-testid="ingredient-template">
            <div
               class="flex flex-col gap-3"
               data-testid="ingredient-template"
               data-index="__INDEX__"
            >
               {{-- MATERIAL --}}
               <select name="materials[__INDEX__][material_id]" class="w-full p-2">
                  <option value="">Select material</option>
                  @foreach ($materials as $material)
                     <option value="{{ $material->id }}">{{ $material->name }}</option>
                  @endforeach
               </select>
               <div class="flex gap-4">
                  {{-- DROPS --}}
                  <input
                     type="number"
                     name="materials[__INDEX__][drops]"
                     placeholder="number of drops"
                     class="w-full p-2"
                  />
                  {{-- DILUTION --}}
                  <select name="materials[__INDEX__][dilution]" class="w-full p-2">
                     <option value="25">25%</option>
                     <option value="10">10%</option>
                     <option value="1">1%</option>
                  </select>
               </div>
            </div>
         </template>

         <div class="flex gap-2">
            <x-primary-button type="submit" class="bg-green-600 hover:bg-green-700">
               SAVE
            </x-primary-button>
            <x-cancel-link href="{{ route('dashboard') }}">CANCEL</x-cancel-link>
         </div>
      </form>
   </div>
</x-app-layout>
