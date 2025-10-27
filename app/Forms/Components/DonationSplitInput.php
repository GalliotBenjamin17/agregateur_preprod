<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class DonationSplitInput extends Field
{
    protected string $view = 'forms.components.donation-split-input';

    public int|Closure $maxAmount = 0;

    protected string|Closure|Htmlable|null $helperText = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerListeners([
            'updateHelperText' => [
                function (Component $component): void {
                    $component->evaluate($component->getHelperText());
                },
            ],
        ]);
    }

    public function maxAmount(int|Closure $amount): static
    {
        $this->maxAmount = $amount;

        return $this;
    }

    public function getMaxAmount(): int
    {
        return $this->maxAmount;
    }

    public function helperText(string|Closure|Htmlable|null $text): static
    {
        $this->helperText = $text;

        return $this;
    }

    public function getHelperText(): string|HtmlString|null
    {
        return $this->evaluate($this->helperText);
    }
}
