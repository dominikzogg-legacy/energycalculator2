<?php

declare(strict_types=1);

namespace Energycalculator\Search;

use Energycalculator\Model\Comestible;

final class ComestibleSearch implements \JsonSerializable
{
    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var int
     */
    private $perPage = 10;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $sort = self::SORT_NAME;

    const SORT_NAME = 'name';

    /**
     * @var string
     */
    private $order = self::ORDER_ASC;

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * @var Comestible[]
     */
    private $elements = [];

    /**
     * @var int
     */
    private $elementCount;

    private function __construct()
    {
    }

    /**
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return (int) $this->page;
    }

    /**
     * @param int $page
     *
     * @return self
     */
    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return (int) $this->perPage;
    }

    /**
     * @param int $perPage
     *
     * @return self
     */
    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     *
     * @return self
     */
    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSort(): string
    {
        return $this->sort;
    }

    /**
     * @param string $sort
     *
     * @return self
     */
    public function setSort(string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @param string $order
     *
     * @return self
     */
    public function setOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return Comestible[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param Comestible[] $elements
     *
     * @return self
     */
    public function setElements(array $elements): self
    {
        $this->elements = $elements;

        return $this;
    }

    /**
     * @return int
     */
    public function getElementCount(): int
    {
        return $this->elementCount;
    }

    /**
     * @param int $elementCount
     *
     * @return self
     */
    public function setElementCount(int $elementCount): self
    {
        $this->elementCount = $elementCount;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $page = (int) $this->page > 0 ? (int) $this->page : 1;
        $perPage = (int) $this->perPage > 0 ? (int) $this->perPage : 1;

        return [
            'page' => $page,
            'perPage' => $perPage,
            'pages' => ceil($this->elementCount / $perPage),
            'sort' => $this->sort,
            'order' => $this->order,
            'elements' => $this->elements,
            'elementCount' => $this->elementCount,
        ];
    }
}
