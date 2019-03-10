<?php

declare(strict_types=1);

namespace Energycalculator\Collection;

interface CollectionInterface extends \JsonSerializable
{
    const PAGE = 1;
    const PER_PAGE = 10;

    /**
     * @param int $page
     */
    public function setPage(int $page);

    /**
     * @return int
     */
    public function getPage(): int;

    /**
     * @param int $perPage
     */
    public function setPerPage(int $perPage);

    /**
     * @return int
     */
    public function getPerPage(): int;

    /**
     * @param string[] $sort
     */
    public function setSort(array $sort);

    /**
     * @return string[]
     */
    public function getSort(): array;

    /**
     * @param string[] $filter
     */
    public function setFilter(array $filter);

    /**
     * @return string[]
     */
    public function getFilter(): array;

    /**
     * @param int $count
     */
    public function setCount(int $count): void;

    /**
     * @return int
     */
    public function getCount(): int;

    /**
     * @param array
     */
    public function setElements(array $items): void;

    /**
     * @return array
     */
    public function getElements(): array;

    /**
     * @return array
     */
    public function jsonSerialize(): array;
}
