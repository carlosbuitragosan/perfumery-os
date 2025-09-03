<?php

namespace App\Livewire;

use App\Models\Material;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialsIndex extends Component
{
    use WithPagination;

    #[Url(as: 'query', except: '')]
    public string $query = '';

    public function updatedQuery(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $materials = Material::search($this->query)
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.materials-index', compact('materials'));
    }
}
