<?php

namespace App\Http\Controllers\Api\News;

use App\Enums\Models\News\NewsStateEnum;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class GetProjectNewController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        return $project->news()->where('state', NewsStateEnum::Published)->with('author:id,first_name,last_name,email,tenant_id')->get();
    }
}
