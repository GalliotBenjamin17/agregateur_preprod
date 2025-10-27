<?php

namespace App\View\Components\Pages\Donations;

use App\Models\Donation;
use Illuminate\View\Component;
use Illuminate\View\View;

class DetailsBase extends Component
{
    public function __construct(
        public Donation $donation
    ) {
        $this->donation->load([
            'related',
            'createdBy',
        ]);
    }

    public function getDisplayedName()
    {
        if ($this->donation->source == 'terminal') {
            return 'Borne interactive - '.format($this->donation->amount).' €';
        }

        if (is_null($this->donation->related)) {
            return 'Non trouvé';
        }

        return $this->donation->related->name.' - '.format($this->donation->amount).' €';
    }

    public function render(): View
    {
        return view('app.donations.details.layouts.base');
    }
}
