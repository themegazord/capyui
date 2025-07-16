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
        public ?bool $prefix = false,
        public ?bool $suffix = false,
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
                    'w-full border focus:border-primary  border-gray-400 outline-none px-2 py-1',
                    'rounded' => !$prefix && !$suffix,
                    'rounded-r' => $prefix && !$suffix,
                    'rounded-l' => !$prefix && $suffix,
                    'rounded-none' => $prefix && $suffix,
                ]) }}
                placeholder="{{ $placeholder }}"
                id="{{ $id }}"
            />
        blade;
    }
}
