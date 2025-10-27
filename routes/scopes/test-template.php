<?php 

// modifier le chargement des images avec asset au lieu de public_patch
if (app()->environment('local')) {
    Route::get('/test/donation-certificate', function () {
        // On récupère une donation existante pour avoir des données réelles
        // Choisir une une donation avec des splits pour un test complet
        $donation = \App\Models\Donation::with('donationSplits.project.parentProject', 'related', 'tenant')
            ->whereHas('donationSplits')
            ->latest()
            ->first();

        if (!$donation) {
            return "Aucune donation avec des splits trouvée pour le test. Veuillez en créer une.";
        }

        return view('pdfs.donation-certificate', [
            'donation' => $donation,
            'related' => $donation->related,
            'donationSplits' => $donation->donationSplits,
            'tenant' => $donation->tenant,
        ]);
    })->name('test.donation-certificate');
}
