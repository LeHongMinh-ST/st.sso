<?php

declare(strict_types=1);

namespace App\View\Components\Table;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableEmpty extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly int $colspan = 1,
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.table.table-empty');
    }
}
