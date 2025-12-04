<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AnalyticsWidget extends Component
{
    public $title;
    public $value;
    public $iconName;
    public $bgColor; 

    /**
     * Create a new component instance.
     *
     * @param string $title The title of the metric (e.g., 'Total Users').
     * @param mixed $value The numerical/string value of the metric (e.g., 1,250).
     * @param string $iconClass Tailwind/FA icon classes (e.g., 'fa-users').
     * @param string $bgColor Background color class (e.g., 'bg-indigo-500').
     */
    public function __construct(string $title, $value, string $iconName = 'bar-chart-2', string $bgColor = 'bg-blue-800')
    {
        $this->title = $title;
        $this->value = $value;
        $this->iconName = $iconName;
        $this->bgColor = $bgColor;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.analytics-widget');
    }
}