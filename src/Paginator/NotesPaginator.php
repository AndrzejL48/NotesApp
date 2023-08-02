<?php

declare(strict_types=1);

namespace App\Paginator;

use App\Paginator\AbstractPaginator;

class NotesPaginator extends AbstractPaginator
{
    protected function defineBaseQuery(): void
    {
        $this->databaseManager->select(['id', 'title', 'created']);
    }
}