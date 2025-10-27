<?php

namespace App\Enums;

enum Roles: string
{
    const Admin = 'admin';

    const LocalAdmin = 'local_admin';

    const Sponsor = 'sponsor';

    const Partner = 'partner';

    // Peut modifier un projet
    const Referent = 'referent';

    const Auditor = 'auditor';

    // Can come Referent and Auditor
    const Member = 'member';

    const Subscriber = 'subscriber';

    const Contributor = 'contributor';

    public static function toDisplay(): array
    {
        return [
            self::Admin => 'Administrateur national',
            self::LocalAdmin => 'Administrateur local',
            self::Auditor => 'Auditeur',
            self::Sponsor => 'Porteur',
            self::Referent => 'Référent',
            self::Contributor => 'Contributeur',
            self::Partner => 'Partenaire',
            self::Subscriber => 'Abonné',
            self::Member => 'Membre',
        ];
    }

    public static function toDashboard(): array
    {
        return [
            self::Auditor => 'Auditeur',
            self::Sponsor => 'Porteur',
            self::Referent => 'Référent',
            self::Contributor => 'Contributeur',
            self::Subscriber => 'Abonné',
            self::Member => 'Membre organisation',
        ];
    }

    public static function toSelect(): array
    {
        return [
            self::Sponsor => 'Porteur',
            self::Referent => 'Référent',
            self::Auditor => 'Auditeur',
            self::Subscriber => 'Abonné',
        ];
    }
}
