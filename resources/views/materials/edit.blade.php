<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Material</h2>
    </x-slot>

    <div class="p-4">
        <form method="POST" action="{{ route('materials.update', $material) }}" class="space-y-3">
            @csrf
            @method('PATCH')

            <label class="block">
                <span class="text-sm">Name *</span>
                <input type="text" name="name" class="p-2 w-full" required
                    value="{{ old('name', $material->name) }}">
                @error('name')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm">Category</span>
                <input type="text" name="category" class="p-2 w-full"
                    value="{{ old('category', $material->category) }}">
            </label>

            <label class="block">
                <span class="text-sm">Botanical</span>
                <input type="text" name="botanical" class="p-2 w-full"
                    value="{{ old('botanical', $material->botanical) }}">
            </label>

            <label class="block">
                <span class="text-sm">Notes</span>
                <textarea name="notes" rows="4" class="p-2 w-full">{{ old('notes', $material->notes) }}</textarea>
            </label>

            <div class="flex gap-2">
                <x-primary-button type="submit">Save</x-primary-button>
                <a href="{{ route('materials.index') }}" class="px-4 py-2 border rounded">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
