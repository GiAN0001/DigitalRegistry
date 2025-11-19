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
            // This is the "Magic": It runs the query dynamically based on your inputs.
            // 1. distinct() -> Ignores duplicates ("counts as one")
            // 2. pluck() -> Gets only the specific column you want
            // 3. sort() -> Arranges them A-Z
            
            $this->options = $model::distinct()
                                    ->orderBy($column)
                                    ->pluck($column);
                                    
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