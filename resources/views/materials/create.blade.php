<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Add Material</h2>
    </x-slot>
    <div class="p-4">
        <form method="post" action="{{ route('materials.store') }}" class="space-y-3">
            @csrf
            <label class="block">
                <span class="text-sm">Name *</span>
                <input name="name" required class="border rounded p-2 w-full" value="{{ old('name') }}">
                @error('name')
                    <div class="text-red-600 text-small">{{ $message }}</div>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm">Category (EO/Absolute/Tincture...)</span>
                <input name="category" class="border rounded p-2 w-full" value="{{ old('category') }}">
            </label>

            <label class="block">
                <span class="text-sm">Botanical (Latin name)</span>
                <input name="botanical" class="border rounded p-2 w-full" value="{{ old('botanical') }}">
            </label>

            <label class="block">
                <span class="text-sm">Notes</span>
                <textarea name="notes" rows="4" class="border rounded p-2 w-full">{{ old('notes') }}</textarea>

            </label>

            <div class="flex gap-2">
                <x-primary-button type="submit">SAVE</x-primary-button>
                <a href="{{ route('materials.index') }}" class="px-4 py-2 border rounded">CANCEL</a>
            </div>
        </form>
    </div>
</x-app-layout>
