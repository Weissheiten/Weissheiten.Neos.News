<?php

namespace Weissheiten\Neos\News\Comparator;

use TYPO3\TYPO3CR\Domain\Model\Node;

class BooleanComparator implements IComparator{

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
        if(is_bool($a->getProperty($propertyName)) && is_bool($b->getProperty($propertyName))){
            if($order){
                return $b[$propertyName] - $a[$propertyName];
            }
            else{
                return $a[$propertyName] - $b[$propertyName];
            }
        }

        throw new ComparatorException(sprintf('Expected comparison for type Boolean for nodes of type '
            . $a->getNodeType()->getName()
            . ' and '
            .$b->getNodeType()->getName()
            .' in property %s', $propertyName, 1482424087));
    }
}