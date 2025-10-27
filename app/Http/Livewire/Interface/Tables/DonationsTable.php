<?php

namespace App\Http\Livewire\Interface\Tables;

use App\Models\Donation;
use App\Models\DonationSplit;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class DonationsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?Organization $organization = null;

    public ?User $user = null;

    protected function getTableQuery(): Builder
    {
        return Donation::with([
            'createdBy',
            'donationSplits',
        ])->withCount([
            'donationSplits',
        ])
            ->when($this->organization, function ($query) {
                return $query->where('related_id', $this->organization->id)
                    ->where('related_type', get_class($this->organization));
            })
            ->when($this->user, function ($query) {
                return $query->where('related_id', $this->user->id)->where('related_type', get_class($this->user));
            });
    }

    protected function getTableActions(): array
    {
        return [

            Action::make('display_pdf')
                ->label('Certificat pdf')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->tooltip('Afficher le certificat')
                ->iconPosition(IconPosition::After)
                ->visible(function (Donation $record) {
                    return $record->certificate_pdf_path;
                })
                ->url(url: function (Donation $record) {
                    return asset($record->certificate_pdf_path);
                }, shouldOpenInNewTab: true),

        ];
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('created_at')
                ->date('d/m/Y')
                ->weight(FontWeight::SemiBold)
                ->description(function (Donation $donation) {
                    return new HtmlString(
                        $donation->donationSplits
                            ->filter(function(DonationSplit $donationSplit) {
                                // If this is a sub-split (has a parent), skip it as we only want to count
                                // either the parent project or its direct sub-projects, not both
                                if ($donationSplit->donation_split_id !== null) {
                                    return true;
                                }

                                // Check if this donation split has any sub-splits
                                $hasChildSplits = $donationSplit->childrenSplits()->exists();

                                if ($hasChildSplits) {
                                    // If it has sub-splits, we'll skip this one and count the children instead
                                    return false;
                                }

                                // Count this split if it's a top-level split with no children
                                return true;
                            })
                            ->pluck('project')
                            ->pluck('name')
                            ->join('<br>')
                    );
                })
                ->label('Date et projets financés'),

            TextColumn::make('amount')
                ->formatStateUsing(fn ($state) => format($state))
                ->description(function (Donation $donation) {

                    $amounts = $donation->donationSplits
                        ->filter(function(DonationSplit $donationSplit) {
                            // If this is a sub-split (has a parent), skip it as we only want to count
                            // either the parent project or its direct sub-projects, not both
                            if ($donationSplit->donation_split_id !== null) {
                                return true;
                            }

                            // Check if this donation split has any sub-splits
                            $hasChildSplits = $donationSplit->childrenSplits()->exists();

                            if ($hasChildSplits) {
                                // If it has sub-splits, we'll skip this one and count the children instead
                                return false;
                            }

                            // Count this split if it's a top-level split with no children
                            return true;
                        })
                        ->pluck('amount');

                    $string = '';

                    foreach ($amounts as $amount) {
                        $string .= format($amount).' € TTC <br>';
                    }

                    return new HtmlString($string);
                })
                ->label('Montant')
                ->prefix('Total: ')
                ->suffix(' € TTC'),

            TextColumn::make('id')
                ->formatStateUsing(function (Donation $donation) {
                    return format($donation->donationSplits->whereNull('donation_split_id')->sum('tonne_co2'));
                })
                ->description(function (Donation $donation) {

                    $tons = $donation->donationSplits
                        ->filter(function(DonationSplit $donationSplit) {
                            // If this is a sub-split (has a parent), skip it as we only want to count
                            // either the parent project or its direct sub-projects, not both
                            if ($donationSplit->donation_split_id !== null) {
                                return true;
                            }

                            // Check if this donation split has any sub-splits
                            $hasChildSplits = $donationSplit->childrenSplits()->exists();

                            if ($hasChildSplits) {
                                // If it has sub-splits, we'll skip this one and count the children instead
                                return false;
                            }

                            // Count this split if it's a top-level split with no children
                            return true;
                        })
                        ->pluck('tonne_co2');

                    $string = '';

                    foreach ($tons as $ton) {
                        $string .= format($ton, 1).'T <br>';
                    }

                    return new HtmlString($string);
                })
                ->label('Équivalent Co2')
                ->prefix('Total: ')
                ->suffix('T'),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    public function render()
    {
        return view('livewire.interface.tables.donations-table');
    }
}
