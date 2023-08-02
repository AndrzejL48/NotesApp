<?php

declare(strict_types=1);

namespace App\Model;

use PDOException;
use App\Exception\StorageException;
use App\Exception\NotFoundException;
use App\Paginator\AbstractPaginator;
use App\Validator\ValidatorManager;

abstract class AbstractModel extends AbstractDatabaseModel
{
    protected string $primaryColumn = 'id';
    protected AbstractPaginator $paginatorClass;
    protected ValidatorManager $validator;

    public function __construct()
    {
        parent::__construct();

        $this->validator = new ValidatorManager();
    }

    public function getAll(): array
    {
        try {
            $paginator = $this->paginatorClass;
            return $paginator->getItems();
        } catch(PDOException $e) {
            throw new StorageException('Failed to get notes data', 400, $e);
        }
    }

    public function getSingle(string $searchBy, string|int $searchValue): array
    {
        try {
            $this->databaseManager->select();
            $this->databaseManager->where($searchBy, $searchValue);
            $this->databaseManager->limit(1);

            $note = $this->databaseManager->fetch();
        } catch(PDOException $e) {
            throw new StorageException('Failed to get note data', 400, $e);
        }

        if (!$note) {
            throw new NotFoundException("Note with id: $searchValue not exists");
        }

        return $note;
    }

    public function create(array $data): void
    {
        try {
            $this->databaseManager->insert($data);
            $this->databaseManager->executeQuery();
        } catch (PDOException $e) {
            throw new StorageException('Failed to create new note', 400, $e);
        }
    }

    
    public function update(array $data, array $target): void
    {
        $targetKey = key($target);
        $targetValue = $target[$targetKey];

        try {
            $this->databaseManager->update($data);
            $this->databaseManager->where($targetKey, $targetValue);
            $this->databaseManager->executeQuery();
        } catch (PDOException $e) {
            throw new StorageException('Failed to update note', 400, $e);
        }
    }

    public function delete(array $target): void
    {
        $targetKey = key($target);
        $targetValue = $target[$targetKey];

        try {
            $this->databaseManager->delete();
            $this->databaseManager->where($targetKey, $targetValue);
            $this->databaseManager->limit(1);
            $this->databaseManager->executeQuery();
        } catch (PDOException $e) {
            throw new StorageException('Failed to delete note', 400, $e);
        }
    }

    public function getAmountOfPages(?string $searchBy, ?string $searchValue): int
    {
        $totalAmountOfNotes = $this->countAll($searchBy, $searchValue);

        $paginator = $this->paginatorClass;
        $pagesAmount = $paginator->getAmountOfPages($totalAmountOfNotes);

        return $pagesAmount;
    }

    
    public function countAll(?string $searchBy = null, ?string $searchValue = null): int
    {
        try {
            $this->databaseManager->count();

            if (!empty($searchBy) && !empty($searchValue)) {
                $this->databaseManager->where(
                    $searchBy,
                    $searchValue,
                    $this->databaseManager::LIKE_OPERATOR
                );
            }

            return $this->databaseManager->fetch($this->databaseManager::FETCH_COLUMN);

        } catch(PDOException $e) {
            throw new StorageException('Failed to get amount of notes', 400, $e);
        }
    }

    public function setSearch(string $searchBy, string $searchValue): void
    {
        $paginator = $this->paginatorClass;
        $paginator->setSearch($searchBy, $searchValue);
    }

    public function setSort(string $sortBy, string $sortOrder): void
    {
        $paginator = $this->paginatorClass;
        $paginator->setSort($sortBy, $sortOrder);
    }

    public function setPageNumber(?int $number = null): void
    {
        if ($number) {
            $paginator = $this->paginatorClass;
            $paginator->setPageNumber($number);
        }
    }

    public function setItemsPerPageAmount(?int $number = null): void
    {
        if ($number) {
            $paginator = $this->paginatorClass;
            $paginator->setItemsPerPageAmount($number);
        }
    }
}