<?php

declare(strict_types=1);

namespace Energycalculator\Collection;

use Energycalculator\Model\ModelInterface;

abstract class AbstractCollection implements CollectionInterface
{
    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $perPage = self::PER_PAGE;

    /**
     * @var string[]
     */
    protected $sort = [];

    /**
     * @var string[]
     */
    protected $filter = [];

    /**
     * @var ModelInterface[]
     */
    protected $elements = [];

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @param int $page
     */
    public function setPage(int $page)
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param string[] $sort
     */
    public function setSort(array $sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return string[]
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    /**
     * @param string[] $filter
     */
    public function setFilter(array $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return string[]
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param ModelInterface[]
     */
    public function setelements(array $elements): void
    {
        $this->elements = $elements;
    }

    /**
     * @return ModelInterface[]
     */
    public function getelements(): array
    {
        return $this->elements;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $page = $this->getPage();
        $perPage = $this->getPerPage();
        $count = $this->getCount();

        $elements = [];
        foreach ($this->getelements() as $item) {
            $elements[] = $item->jsonSerialize();
        }

        return [
            'page' => $page,
            'perPage' => $perPage,
            'pages' => ceil($count / $perPage),
            'sort' => $this->getSort(),
            'count' => $count,
            'elements' => $elements,
        ];
    }
}
