<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ChartWidget extends Component
{
    public $chartId;
    public $title;
    public $type;
    public $dataJson; // Chart data, passed as JSON string

    /**
     * Create a new component instance.
     *
     * @param string $title The title of the chart card.
     * @param string $type The Chart.js type (e.g., 'bar', 'line', 'doughnut').
     * @param array $data The data structure containing labels, datasets, etc.
     */
    public function __construct(string $title, string $type, array $data)
    {
        $this->title = $title;
        $this->type = strtolower($type); // Ensure type is lowercase for JS
        $this->chartId = 'chart-' . uniqid(); // Generate unique ID for canvas element
        $this->dataJson = json_encode($data); // CRITICAL: Convert array to JSON string for frontend
    }

    public function render(): View
    {
        return view('components.chart-widget');
    }
}