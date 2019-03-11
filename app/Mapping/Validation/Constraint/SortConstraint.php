<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Validation\Constraint;

use Chubbyphp\Validation\Constraint\ConstraintInterface;
use Chubbyphp\Validation\ValidatorContextInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Chubbyphp\Validation\ValidatorLogicException;
use Chubbyphp\Validation\Error\ErrorInterface;
use Chubbyphp\Validation\Error\Error;

final class SortConstraint implements ConstraintInterface
{
    /**
     * @var string[]
     */
    private $allowedFields;

    const ALLOWED_ORDERS = ['asc', 'desc'];

    /**
     * @param array $allowedFields
     */
    public function __construct(array $allowedFields)
    {
        $this->allowedFields = $allowedFields;
    }

    /**
     * @param string                    $path
     * @param mixed                     $sort
     * @param ValidatorContextInterface $context
     * @param ValidatorInterface|null   $validator
     *
     * @return ErrorInterface[]
     *
     * @throws ValidatorLogicException
     */
    public function validate(
        string $path,
        $sort,
        ValidatorContextInterface $context,
        ValidatorInterface $validator = null
    ): array {
        if (!is_array($sort)) {
            return [new Error(
                $path,
                'constraint.sort.invalidtype',
                ['type' => is_object($sort) ? get_class($sort) : gettype($sort)]
            )];
        }

        $errors = [];

        foreach ($sort as $field => $order) {
            if (!in_array($field, $this->allowedFields, true)) {
                $errors[] = new Error(
                    $path,
                    'constraint.sort.field.notallowed',
                    ['field' => $field, 'allowedFields' => $this->allowedFields]
                );
            }

            if (!is_string($order)) {
                $errors[] = new Error(
                    $path,
                    'constraint.sort.order.invalidtype',
                    ['field' => $field, 'type' => is_object($order) ? get_class($order) : gettype($order)]
                );

                continue;
            }

            if (!in_array($order, self::ALLOWED_ORDERS, true)) {
                $errors[] = new Error(
                    $path,
                    'constraint.sort.order.notallowed',
                    ['field' => $field, 'order' => $order, 'allowedOrders' => self::ALLOWED_ORDERS]
                );
            }
        }

        return $errors;
    }
}
