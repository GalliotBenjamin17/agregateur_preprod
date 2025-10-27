<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    const SIZE_SM = 'sm';

    const SIZE_MD = 'md';

    const SIZE_LG = 'lg';

    const VALID_SIZES = [self::SIZE_SM, self::SIZE_MD, self::SIZE_LG];

    const TYPE_DEFAULT = 'default';

    const TYPE_INFO = 'info';

    const TYPE_SUCCESS = 'success';

    const TYPE_DANGER = 'danger';

    const TYPE_DANGEROUS = 'dangerous';

    const TYPE_WARNING = 'warning';

    const VALID_TYPES = [self::TYPE_DEFAULT, self::TYPE_INFO, self::TYPE_SUCCESS, self::TYPE_DANGER, self::TYPE_DANGEROUS, self::TYPE_WARNING];

    public function __construct(
        public string $size = self::SIZE_SM,
        public string $type = self::TYPE_DEFAULT)
    {
        if (! in_array($size, self::VALID_SIZES)) {
            $size = self::SIZE_SM;
        }
        $this->size = $size;

        if (! in_array($type, self::VALID_TYPES)) {
            $type = self::TYPE_DEFAULT;
        }
        $this->type = $type;
    }

    public function render()
    {
        return view('components.button');
    }
}
