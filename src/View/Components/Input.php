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
        public ?string $prefix = null,
        public ?string $suffix = null,
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
                $hasErrors = $errors->has($getModelName());
            @endphp

            <div>
                @if ($label && !$inline)
                    <div class="flex flex-col gap-1 m-4 {{ $attributes->get('class', '') }}" x-data="{
                                focused: false,
                                content: '',
                                isDirty() {
                                    return this.content !== '';
                                }
                            }">
                        <label for="{{ $uuid }}" class="font-bold text-xs">{{ $label }}</label>
                        <div @class(['flex items-center' => $prefix || $suffix])>
                            @if ($prefix)
                                <span @class([
                                    "border rounded-l px-2 py-2 flex items-center",
                                    "border-gray-300" => !$hasErrors,
                                    "border-error" => $hasErrors
                                ])
                                    :class="{ 'border-primary': focused }"
                                >
                                    <i class="{{ $prefix }}"></i>
                                </span>
                            @endif
                            <x-capy-input
                                placeholder="{{ $getPlaceholder() }}"
                                id="{{ $uuid }}"
                                prefix="{{ $prefix }}"
                                suffix="{{ $suffix }}"
                                @focus="focused = true"
                                @blur="focused = false"
                                hasErrors="{{$hasErrors}}"
                            />
                            @if ($suffix)
                                <span @class([
                                    "border rounded-r px-2 py-2 flex items-center",
                                    "border-gray-300" => !$hasErrors,
                                    "border-error" => $hasErrors
                                ])
                                    :class="{ 'border-primary': focused }"
                                >
                                    <i class="{{ $suffix }}"></i>
                                </span>
                            @endif
                        </div>
                        @if($hasErrors)
                            @foreach($errors->get($getModelName()) as $error)
                                <span class="text-error text-sm">{{ $error }}</span>
                            @endforeach
                        @endif
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
                            @class([
                                'absolute top-[-8px] left-2 bg-base-100 text-primary text-xs px-1 font-bold pointer-events-none',
                                'left-10' => $prefix
                            ])
                            for="{{ $uuid }}"
                        >
                            {{ $label }}
                        </label>
                        <div @class(['flex items-center' => $prefix || $suffix])>
                            @if ($prefix)
                                <span class="border border-gray-300 rounded-l px-2 py-2 flex items-center"
                                    :class="{ 'border-primary': focused }"
                                >
                                    <i class="{{ $prefix }}"></i>
                                </span>
                            @endif
                            <x-capy-input placeholder="{{ $getPlaceholder() }}" id="{{ $uuid }}" prefix="{{ $prefix }}" suffix="{{ $suffix }}" x-model="content" @focus="focused = true" @blur="focused = false" />
                            @if ($suffix)
                                <span class="border border-gray-300 rounded-r px-2 py-2 flex items-center"
                                    :class="{ 'border-primary': focused }"
                                >
                                    <i class="{{ $suffix }}"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        blade;
    }
}
