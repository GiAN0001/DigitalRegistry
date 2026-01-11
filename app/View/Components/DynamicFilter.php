<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class DynamicFilter extends Component
{
    public $options;
    public $title;
    public $column;

    /**
     * Create a new component instance.
     *
     * @param string $model  The full class path of the model (e.g., 'App\Models\AreaStreet')
     * @param string $column The column to fetch (e.g., 'purok_name')
     * @param string $title  The label for the button
     */
    public function __construct($model, $column, $title = 'Select...')
    {
        $this->title = $title;
        $this->column = $column;

        try {

           if ($column === 'created_at') {
            $this->options = $model::selectRaw("YEAR(created_at) as year")
                                    ->distinct()
                                    ->orderBy('year', 'desc')
                                    ->pluck('year');
            } else {
                // Standard distinct logic for other columns (Purok, Street, etc.)
                $this->options = $model::distinct()
                                        ->orderBy($column)
                                        ->pluck($column);
            }
                                    
        } catch (\Exception $e) {
            // Safety catch in case the model/column is wrong
            Log::error("DynamicFilter Error: " . $e->getMessage());
            $this->options = collect([]); 
        }
    }

    public function render(): View
    {
        return view('components.dynamic-filter');
    }
}