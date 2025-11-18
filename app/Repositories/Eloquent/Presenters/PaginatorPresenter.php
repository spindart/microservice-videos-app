<?php

namespace App\Repositories\Eloquent\Presenters;

use Core\Domain\Repository\PaginationInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use stdClass;

class PaginatorPresenter implements PaginationInterface
{
    /**
     * @return StdClass[]
     */
    protected array $items = [];

    public function __construct(protected LengthAwarePaginator $paginator)
    {
        $this->items = $this->resolveItems($this->paginator->items());
    }

    /**
     * @return StdClass[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->paginator->total();
    }

    public function currentPage(): int
    {
        return $this->paginator->currentPage();
    }

    public function perPage(): int
    {
        return $this->paginator->perPage();
    }

    public function lastPage(): int
    {
        return $this->paginator->lastPage();
    }

    public function from(): int
    {
        return $this->paginator->lastItem();
    }

    public function to(): int
    {
        return $this->paginator->firstItem();
    }

    public function firstPage(): int
    {
        return $this->paginator->firstItem();
    }

    public function nextPage(): int
    {
        return $this->paginator->hasMorePages()
            ? $this->paginator->currentPage() + 1
            : 0;
    }

    public function previousPage(): int
    {
        return $this->paginator->currentPage() > 1
            ? $this->paginator->currentPage() - 1
            : 0;
    }

    private function resolveItems(array $items): array
    {
        $response = [];
        foreach ($items as $item) {
            $stdClass = new stdClass();
            foreach ($item->toArray() as $key => $value) {
                $stdClass->{$key} = $value;
            }
            array_push($response, $stdClass);
        }
        return $response;
    }
}
