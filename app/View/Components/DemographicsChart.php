<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class DemographicsChart extends Component
{
    public $chartId;
    public $title;
    public $dataJson; 
    public $type = 'bar'; // Hardcoded type
    public $optionsJson; // Dedicated options property

    public function __construct(string $title, array $data)
    {
        $this->title = $title;
        $this->chartId = 'chart-demo-' . uniqid();
        $this->dataJson = json_encode($data); 
        
        // Define specific options for the Horizontal Bar Chart
        $options = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'indexAxis' => 'y', // CRITICAL: Makes the chart horizontal
            'scales' => [
                'y' => ['beginAtZero' => true],
                'x' => ['beginAtZero' => true],
            ],
            'plugins' => [
                'legend' => ['display' => false], // Hide the legend since colors are repetitive
            ],
        ];
        $this->optionsJson = json_encode($options);
    }

    public function render(): View
    {
        // We will reuse the general chart-widget Blade view for the rendering logic
        return view('components.demographics-chart'); 
    }
}