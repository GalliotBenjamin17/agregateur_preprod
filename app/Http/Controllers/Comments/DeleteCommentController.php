<?php

namespace App\Http\Controllers\Comments;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class DeleteCommentController extends Controller
{
    public function __invoke(Request $request, Comment $comment)
    {
        $comment->delete();

        \Session::flash('success', 'Le commentaire a été supprimé de cette page.');

        return back();
    }
}
