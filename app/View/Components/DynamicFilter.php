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
    public $isMap = false;

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
            } 
            // ADDED: Logic for Month Filtering
            elseif ($column === 'date') {
                $this->isMap = true;
                $this->options = $model::selectRaw("MONTH(date) as month_num")
                                        ->distinct()
                                        ->orderBy('month_num', 'asc')
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            return [$item->month_num => \Carbon\Carbon::create()->month($item->month_num)->format('F')];
                                        });
            } else {
                $this->options = $model::distinct()->orderBy($column)->pluck($column);
            }
        } catch (\Exception $e) {
            Log::error("DynamicFilter Error: " . $e->getMessage());
            $this->options = collect([]); 
        }
    }

    public function render(): View
    {
        return view('components.dynamic-filter');
    }
}