<?php

namespace App\Http\Controllers\Comments;

use App\Helpers\ActivityHelper;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class StoreCommentController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required',
            'related_id' => 'required',
            'related_type' => 'required',
        ]);

        $validated['created_by'] = $request->user()->id;

        $comment = Comment::create($validated);

        $comment->load([
            'related',
        ]);

        ActivityHelper::push(
            performedOn: $comment,
            title: "Ajout d'un nouveau commentaire.",
            description: $comment->related?->name ? 'Modèle: '.$comment->related?->name : null,
            url: method_exists($comment->related, 'redirectRouter') ? $comment->related->redirectRouter() : null
        );

        \Session::flash('success', 'Le commentaire a été ajouté sur cette page.');

        return back();
    }
}
