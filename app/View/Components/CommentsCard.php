<?php

namespace App\View\Components;

use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class CommentsCard extends Component
{
    public Collection $comments;

    public function __construct(
        public Model $model
    ) {
        $this->comments = Comment::with('createdBy')->where('related_id', $this->model->id)->get();
    }

    public function render(): View
    {
        return view('components.comments-card');
    }
}
