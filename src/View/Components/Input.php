<?php

namespace Capy\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $label = null,
        public ?bool $inline = false,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'blade'
            @if($label && !$inline)
                <div class="m-4 relative border rounded">
                    <input type="email" class="peer w-full placeholder:text-transparent py-1 text-lg" placeholder="name" />
                    <label class="font-semibold peer-focus:my-1 absolute left-0 top-1 ml-1 -translate-y-3 bg-white px-1 text-lg duration-100 ease-linear
                                peer-placeholder-shown:translate-y-0 peer-placeholder-shown:text-lg peer-placeholder-shown:text-black
                                peer-focus:ml-1 peer-focus:-translate-y-5 peer-focus:px-1 peer-focus:text-lg"
                        placeholder="{{$label}}">
                        {{ $label }}
                    </label>
                </div>
            @endif
            @if($label && $inline)
                <fieldset class="m-4 relative">
                    <legend class="font-bold">{{ $label }}</legend>
                    <input type="email" class="peer w-full border-b placeholder:text-transparent" placeholder="name" />
                </fieldset>
            @endif
        blade;
    }
}
