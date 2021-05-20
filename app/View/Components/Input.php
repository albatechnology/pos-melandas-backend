<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Input extends Component
{
    const TYPE_TEXT   = 'text';
    const TYPE_NUMBER = 'number';

    public $label;
    public $label_helper;
    public $input_type;

    public function __construct(
        public string $key,
        public mixed $value,
        string $labelKey,
        public string $type = 'text',
    )
    {
        $this->input_type = match ($type) {
            self::TYPE_NUMBER => 'number',
            default => 'text'
        };

        $this->label        = trans($labelKey);
        $this->label_helper = trans($labelKey . '_helper');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return match ($this->type) {
            self::TYPE_NUMBER => view('components.input_number'),
            default => view('components.input_text')
        };
    }
}