<?php

namespace Energycalculator\Model\Traits;

trait CloneWithModificationTrait
{
    /**
     * @var array
     */
    private $__modifications = [];

    /**
     * @param string $method
     * @param mixed  $new
     * @param mixed  $old
     *
     * @return self
     */
    private function cloneWithModification(string $method, $new, $old)
    {
        $model = clone $this;
        $model->__modifications[] = [
            'method' => $method,
            'new' => $new,
            'old' => $old,
        ];

        return $model;
    }
}
