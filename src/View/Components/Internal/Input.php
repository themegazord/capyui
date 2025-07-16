<?php

namespace Capy\View\Components\Internal;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Input extends Component
{
    public function __construct(
        public ?string $id = null,
        public ?string $placeholder = null,
    ){

    }
    public function render(): View|Closure|string
    {
        return <<<'blade'
            @php
                $inputAttributes = $attributes->merge(['type' => 'text']);
            @endphp
            <input
                {{ $inputAttributes->class([
                    'w-full rounded border border-gray-400 focus:border-primary outline-none px-2 py-1'
                ]) }}
                placeholder="{{ $placeholder }}"
                id="{{ $id }}"
            />
        blade;
    }
}
