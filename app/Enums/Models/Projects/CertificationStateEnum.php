<?php

namespace App\Enums\Models\Projects;

enum CertificationStateEnum: string
{
    case Notified = 'notified';
    case PendingEvaluation = 'pending_evaluation';
    case Evaluated = 'evaluated';
    case Certified = 'certified';
    case Verified = 'verified';
    case Approved = 'approved';

    public function databaseKey(): string
    {
        return match ($this) {
            self::Notified => self::Notified->value,
            self::PendingEvaluation => self::PendingEvaluation->value,
            self::Evaluated => self::Evaluated->value,
            self::Certified => self::Certified->value,
            self::Verified => self::Verified->value,
            self::Approved => self::Approved->value,
        };
    }

    public function displayName(): string
    {
        return match ($this) {
            self::Notified => 'Déposé',
            self::PendingEvaluation => 'Evaluation',
            self::Evaluated => 'Évalué',
            self::Certified => 'Labellisé',
            self::Verified => 'Audité',
            self::Approved => 'Vérifié',
        };
    }

    public function humanName(): string
    {
        return $this->displayName();
    }

    public function rank(): int
    {
        return match ($this) {
            self::Notified => 1,
            self::PendingEvaluation => 2,
            self::Evaluated => 3,
            self::Certified => 4,
            self::Verified => 5,
            self::Approved => 6,
        };
    }

    public static function toArray(): array
    {
        return [
            self::Notified->databaseKey() => CertificationStateEnum::Notified->displayName(),
            self::PendingEvaluation->databaseKey() => CertificationStateEnum::PendingEvaluation->displayName(),
            self::Evaluated->databaseKey() => CertificationStateEnum::Evaluated->displayName(),
            self::Certified->databaseKey() => CertificationStateEnum::Certified->displayName(),
            self::Verified->databaseKey() => CertificationStateEnum::Verified->displayName(),
            self::Approved->databaseKey() => CertificationStateEnum::Approved->displayName(),
        ];
    }

    public function description(): string
    {
        return match ($this) {
            self::Notified => 'Une méthode a été choisi et le projet a été déposé.',
            self::PendingEvaluation => 'Le projet est en cours d\'évaluation.',
            self::Evaluated => 'Le projet a été évalué',
            self::Certified => 'Le projet a été labellisé.',
            self::Verified => 'Le projet a été audité.',
            self::Approved => 'Le projet a été vérifié.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Notified => <<<'blade'
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mr-2 h-5 w-5 flex-shrink-0">
                  <path fill-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 013.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 013.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 01-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875zM12.75 12a.75.75 0 00-1.5 0v2.25H9a.75.75 0 000 1.5h2.25V18a.75.75 0 001.5 0v-2.25H15a.75.75 0 000-1.5h-2.25V12z" clip-rule="evenodd" />
                  <path d="M14.25 5.25a5.23 5.23 0 00-1.279-3.434 9.768 9.768 0 016.963 6.963A5.23 5.23 0 0016.5 7.5h-1.875a.375.375 0 01-.375-.375V5.25z" />
                </svg>
            blade,
            self::PendingEvaluation => <<<'blade'
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mr-2 h-5 w-5 flex-shrink-0">
                  <path fill-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 013.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 013.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 01-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875zM9.75 17.25a.75.75 0 00-1.5 0V18a.75.75 0 001.5 0v-.75zm2.25-3a.75.75 0 01.75.75v3a.75.75 0 01-1.5 0v-3a.75.75 0 01.75-.75zm3.75-1.5a.75.75 0 00-1.5 0V18a.75.75 0 001.5 0v-5.25z" clip-rule="evenodd" />
                  <path d="M14.25 5.25a5.23 5.23 0 00-1.279-3.434 9.768 9.768 0 016.963 6.963A5.23 5.23 0 0016.5 7.5h-1.875a.375.375 0 01-.375-.375V5.25z" />
                </svg>
            blade,
            self::Evaluated => <<<'blade'
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mr-2 h-5 w-5 flex-shrink-0">
                  <path fill-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 013.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 013.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 01-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875zm6.905 9.97a.75.75 0 00-1.06 0l-3 3a.75.75 0 101.06 1.06l1.72-1.72V18a.75.75 0 001.5 0v-4.19l1.72 1.72a.75.75 0 101.06-1.06l-3-3z" clip-rule="evenodd" />
                  <path d="M14.25 5.25a5.23 5.23 0 00-1.279-3.434 9.768 9.768 0 016.963 6.963A5.23 5.23 0 0016.5 7.5h-1.875a.375.375 0 01-.375-.375V5.25z" />
                </svg>
            blade,
            self::Certified => <<<'blade'
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mr-2 h-5 w-5 flex-shrink-0">
                  <path fill-rule="evenodd" d="M9 1.5H5.625c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5zm6.61 10.936a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 14.47a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                  <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                </svg>
            blade,
            self::Verified => <<<'blade'
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mr-2 h-5 w-5 flex-shrink-0">
                  <path d="M11.625 16.5a1.875 1.875 0 100-3.75 1.875 1.875 0 000 3.75z" />
                  <path fill-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 013.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 013.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 01-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875zm6 16.5c.66 0 1.277-.19 1.797-.518l1.048 1.048a.75.75 0 001.06-1.06l-1.047-1.048A3.375 3.375 0 1011.625 18z" clip-rule="evenodd" />
                  <path d="M14.25 5.25a5.23 5.23 0 00-1.279-3.434 9.768 9.768 0 016.963 6.963A5.23 5.23 0 0016.5 7.5h-1.875a.375.375 0 01-.375-.375V5.25z" />
                </svg>
            blade,
            self::Approved => <<<'blade'
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mr-2 h-5 w-5 flex-shrink-0">
                  <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd" />
                  <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                </svg>

            blade,
        };
    }
}
