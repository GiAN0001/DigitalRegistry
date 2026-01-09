<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class FormSelect extends Component
{
    public $options;
    public $placeholder;

    /**
     * Create a new component instance.
     *
     * @param string $model       The full class path (e.g., 'App\Models\PetType')
     * @param string $column      The column to display (e.g., 'name')
     * @param string $valueColumn (Optional) The column to use as the value (default is same as display)
     * @param string $placeholder The default text (e.g., 'Select Type')
     */
    public function __construct($model, $column, $valueColumn = null, $placeholder = 'Select...')
    {
        $this->placeholder = $placeholder;

        try {
         
            $valueColumn = $valueColumn ?? $column;

           
            $this->options = $model::distinct()
                                    ->pluck($column)
                                    ->all();
                                    
        } catch (\Exception $e) {
            Log::error("FormSelect Error for {$model}: " . $e->getMessage());
            $this->options = collect([]); 
        }
    }

    public function render(): View
    {
        return view('components.form-select');
    }
}