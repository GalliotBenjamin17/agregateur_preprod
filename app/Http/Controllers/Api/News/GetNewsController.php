<?php

namespace App\Http\Controllers\Api\News;

use App\Enums\Models\News\NewsStateEnum;
use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class GetNewsController extends Controller
{
    public function __invoke(Request $request)
    {
        $newsQuery = News::query()
            ->where('state', NewsStateEnum::Published)
            ->with([
                'author:id,first_name,last_name,email,tenant_id',
            ])
            ->when($request->has('tenant'), function ($query) use ($request) {
                return $query->where('tenant_id', $request->get('tenant'));
            });

        if ($request->boolean('count')) {
            return $newsQuery->count();
        }

        return $newsQuery->get()->each(function (News $item) {

        });
    }
}
