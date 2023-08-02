<?php

declare(strict_types=1);

namespace App\Paginator;

use App\Model\AbstractDatabaseModel;

abstract class AbstractPaginator extends AbstractDatabaseModel
{
    private const DEFAULT_PAGE_NUMBER = 1;
    private const DEFAULT_ITEMS_PER_PAGE_AMOUNT = 10;

    private ?int $pageNumber = null;
    private ?int $itemsPerPageAmount = null;
    private array $baseQueryParameters = [];

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
        parent::__construct();
    }

    abstract protected function defineBaseQuery(): void;

    public function getItems()
    {
        $this->defineBaseQuery();

        if (!empty($this->baseQueryParameters)) {
            $this->addParametersToBaseQuery();
        }

        $this->addPaginationToQuery();

        return $this->databaseManager->fetch($this->databaseManager::FETCH_ALL);
    }

    private function addParametersToBaseQuery(): void
    {
        $parameters = $this->baseQueryParameters;

        foreach ($parameters as $key => $params) {
            $callMethod = 'add' . ucfirst($key) . 'ToQuery';
            if (method_exists($this, $callMethod)) {
                $this->$callMethod($params);
            }
        }
    }

    protected function addSearchToQuery(array $params): void
    {
        $this->databaseManager->where(
            $params['search_by'],
            $params['search_value'],
            $this->databaseManager::LIKE_OPERATOR
        );
    }

    protected function addSortToQuery(array $params): void
    {
        $this->databaseManager->sort(
            $params['sort_by'],
            $params['sort_order']
        );
    }

    protected function addPaginationToQuery(): void
    {
        $amount = $this->itemsPerPageAmount ?? self::DEFAULT_ITEMS_PER_PAGE_AMOUNT;
        $offset = $this->calculateOffset();

        $this->databaseManager->limit($amount);
        $this->databaseManager->offset($offset);
    }

    private function calculateOffset(): int
    {
        $pageNumber = $this->pageNumber ?? self::DEFAULT_PAGE_NUMBER;
        $itemsPerPageAmount = $this->itemsPerPageAmount ?? self::DEFAULT_ITEMS_PER_PAGE_AMOUNT;
        $offset = ($pageNumber - 1) * $itemsPerPageAmount;

        return $offset;
    }

    public function getAmountOfPages($totalAmountOfNotes): int
    {
        $itemsAmount = $this->itemsPerPageAmount ?? self::DEFAULT_ITEMS_PER_PAGE_AMOUNT;
        $pagesAmount = (int) ceil($totalAmountOfNotes / $itemsAmount);

        return $pagesAmount;
    }

    public function setSearch(string $searchBy, string $searchValue): void
    {
        $this->baseQueryParameters['search']['search_by'] = $searchBy;
        $this->baseQueryParameters['search']['search_value'] = $searchValue;
    }

    public function setSort(string $sortBy, string $sortOrder): void
    {
        $this->baseQueryParameters['sort']['sort_by'] = $sortBy;
        $this->baseQueryParameters['sort']['sort_order'] = $sortOrder;
    }

    public function setPageNumber(int $number): void
    {
        $this->pageNumber = $number;
    }

    public function setItemsPerPageAmount(int $number): void
    {
        $this->itemsPerPageAmount = $number;
    }
}