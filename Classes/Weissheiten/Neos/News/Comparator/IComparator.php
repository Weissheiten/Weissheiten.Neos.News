<?php

namespace Weissheiten\Neos\News\Comparator;

use TYPO3\TYPO3CR\Domain\Model\Node;

/**
 * Interface Comparator
 * @package Weissheiten\Neos\News\Comparator
 *
 * Interface for comparators (used with usort)
 */
interface IComparator{
    /**
     * @param Node $a node to compare with $b
     * @param Node $b node to compare with $a
     * @param string $propertyName node property name
     * @param bool $order order for sorting (ASC = true or DESC = false)
     * @return mixed
     */
    function execute($a, $b, $propertyName, $order);
}