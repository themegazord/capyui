<?php

namespace Capy\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public ?string $uuid = null;
    public function __construct(
        public ?string $label = null,
        public bool $inline = false,
    ) {
        $this->uuid = "capy_" . md5(serialize($this));
    }

    public function getPlaceholder(): string
    {
        return $this->attributes->get('placeholder', '');
    }

    public function getModelName(): ?string
    {
        return $this->attributes->get('wire:model');
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
            @php
                $uuid = $getModelName() . $uuid;
                $hasWidthClass = str($attributes->get('class'))->contains('w-');
                $containerClass = $attributes->get('class');
                $widthClass = $hasWidthClass ? $containerClass : 'w-full';
            @endphp

            <div>
                @if ($label && !$inline)
                    <div class="flex flex-col gap-1 m-4 {{ $attributes->get('class', '') }}" >
                        <label for="{{ $uuid }}" class="font-bold text-xs">{{ $label }}</label>
                        <x-capy-input placeholder="{{ $getPlaceholder() }}" id="{{ $uuid }}"/>
                    </div>
                @elseif ($label && $inline)
                    <div
                        x-data="{
                            focused: false,
                            content: '',
                            isDirty() {
                                return this.content !== '';
                            }
                        }"
                        class="relative m-4 border border-gray-300 rounded {{ $widthClass }}"
                        :class="{ 'border-primary': focused || isDirty() }"
                    >
                        <label
                            x-show="focused || isDirty()"
                            x-transition.opacity.duration.200ms
                            class="absolute top-[-8px] left-2 bg-base-100 text-primary text-xs px-1 font-bold pointer-events-none"
                            for="{{ $uuid }}"
                        >
                            {{ $label }}
                        </label>

                        <x-capy-input placeholder="{{ $getPlaceholder() }}" id="{{ $uuid }}" x-model="content" @focus="focused = true" @blur="focused = false" />
                    </div>
                @endif
            </div>
        blade;
    }
}
