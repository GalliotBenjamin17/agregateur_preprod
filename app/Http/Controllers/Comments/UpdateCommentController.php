<?php

namespace App\Http\Controllers\Comments;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class UpdateCommentController extends Controller
{
    public function __invoke(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'required',
        ]);

        $comment->update($validated);

        \Session::flash('success', 'Le commentaire a été mis à jour.');

        return back();
    }
}
