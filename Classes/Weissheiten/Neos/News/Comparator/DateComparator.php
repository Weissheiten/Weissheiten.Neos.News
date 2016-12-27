<?php

namespace Weissheiten\Neos\News\Comparator;

use TYPO3\TYPO3CR\Domain\Model\Node;

class DateComparator implements IComparator{

    /**
     * Compares Dates - a property which is not a Date is considered lesser than any property with a data
     *
     * @param Node $a node to compare with $b
     * @param Node $b node to compare with $a
     * @param string $propertyName node property name
     * @param bool $order order for sorting (ASC = true or DESC = false)
     * @return int
     * @throws ComparatorException
     */
    function execute($a, $b, $propertyName, $order)
    {
        $a_value = $a->getProperty($propertyName);
        $b_value = $b->getProperty($propertyName);

        $a_isdate = ($a_value instanceof DateTime);
        $b_isdate = ($b_value instanceof DateTime);

        if(!$a_isdate){
            return ($order) ? 1 : -1;
        }
        if(!$b_isdate){
            return ($order) ? -1 : 1;
        }

        if($a[$propertyName] < $b[$propertyName]){
            return ($order) ? 1 : -1;
        }
        return ($order) ? -1 : 1;
    }
}