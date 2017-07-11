<?php

declare(strict_types=1);

namespace Energycalculator\Twig;

final class NumericExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('is_numeric', 'is_numeric'),
        ];
    }
}
