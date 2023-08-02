<?php

declare(strict_types=1);

namespace App\Model;

use App\Paginator\NotesPaginator;
use App\Exception\InvalidFieldException;

class NotesModel extends AbstractModel
{
    private const TABLE_NAME = 'notes';
    private const PRIMARY_COLUMN = 'id';

    public function __construct()
    {
        $this->tableName = self::TABLE_NAME;
        $this->primaryColumn = self::PRIMARY_COLUMN;

        parent::__construct();

        $this->paginatorClass = new NotesPaginator(self::TABLE_NAME);
    }

    public function setSort(string $sortBy, string $sortOrder): void
    {
        $isValid = $this->validator->validate(
            'AreValuesAllowed',
            [
                $sortOrder => [
                    'asc',
                    'desc'
                ],
                $sortBy => [
                    'id',
                    'title',
                    'created'
                ]
            ]
        );

        if ($isValid) {
            parent::setSort($sortBy, $sortOrder);
        } else {
            $invalidElements = $this->validator->getInvalidElements(false);
            throw new InvalidFieldException("Invalid sort by parameters $invalidElements", 400);
        }
    }
}
