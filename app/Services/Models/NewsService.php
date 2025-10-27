<?php

namespace App\Services\Models;

use App\Models\News;

class NewsService
{
    public function __construct(
        public ?News $news = null
    ) {
    }

    public function store(array $data): News
    {
        $this->news = News::create($data);

        return $this->news;
    }

    public function update(array $data): News
    {
        $this->news->update($data);

        return $this->news->refresh();
    }
}
