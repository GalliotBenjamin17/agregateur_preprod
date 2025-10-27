<?php

namespace App\Http\Controllers\Interface\Donations;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\Models\TransactionService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class CreateDonationFromUrlController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'projectId' => 'required',
            'amount' => 'required',
        ]);

        $project = Project::findOr($request->get('projectId'), function () {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => [
                    'projectId' => 'Project does not exist',
                ],
            ]));
        });

        $transactionService = new TransactionService($project->tenant);
        $transaction = $transactionService->createTransaction(
            related: auth()->user(),
            amount: $request->get('amount'),
            project: $project,
            failedUrl: $project->tenant->public_url."?p={$request->get('wpPageId')}&payment_cancel=1"
        );

        return redirect()->to($transaction->payment_url);
    }
}
