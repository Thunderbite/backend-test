<?php

namespace App\Http\Livewire\Backstage\Components\Imports;

use App\Http\Livewire\Backstage\TableComponent;
use App\Models\ExcludeImport;
use App\Models\Import;
use App\Models\ImportRow;

class RowsTable extends TableComponent
{
    public $sortField = 'created_at';

    public $import;

    public $hasSearch = false;

    public function mount(Import $import)
    {
        $this->import = $import;
    }

    public function render()
    {
        $columns = [
            [
                'title' => 'Row №',
                'attribute' => 'row_number',
                'sortField' => 'row_number',
                'sort' => true,
            ],
            [
                'title' => 'Contents',
                'attribute' => 'contents',
                'sortField' => 'contents',
                'sort' => true,
            ],
            [
                'title' => 'Status',
                'attribute' => 'errors',
                'sortField' => 'errors',
                'sort' => true,
            ],
        ];

        return view('livewire.backstage.import-rows-table', [
            'columns' => $columns,
            'resource' => 'excludes.imports',
            'rows' => ImportRow::search($this->search)
                ->where('import_id', $this->import->id)
                ->orderBy($this->sortField, $this->sortAsc ? 'DESC' : 'ASC')
                ->paginate($this->perPage),
        ]);
    }
}
