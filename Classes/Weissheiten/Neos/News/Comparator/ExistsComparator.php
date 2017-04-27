<?php

namespace Weissheiten\Neos\News\Comparator;

use Neos\ContentRepository\Domain\Model\Node;

class ExistsComparator implements IComparator{

    /**
     * @param Node $a node to compare with $b
     * @param Node $b node to compare with $a
     * @param string $propertyName node property name
     * @param bool $order order for sorting (ASC = true or DESC = false)
     * @return int
     * @throws ComparatorException
     */
    function execute($a, $b, $propertyName, $order)
    {
        $a_value = is_null($a->getProperty($propertyName));
        $b_value = is_null($b->getProperty($propertyName));

        if($a_value === $b_value){
            return 0;
        }

        if($a_value){
            return ($order) ? -1 : 1;
        }

        return ($order) ? 1 : -1;

    }
}