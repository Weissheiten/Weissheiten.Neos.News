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
        $a_value = $a->getProperty($propertyName);
        $b_value = $b->getProperty($propertyName);

        // null is considered false for comparison
        if($a_value===null){
            $a_value = false;
        }

        if($b_value===null){
            $b_value = false;
        }

        if(is_bool($a_value) && is_bool($b_value)){
            if($order){
                return $a_value -  $b_value ;
            }
            else{
                return  $b_value  - $a_value;
            }
        }

        // if no result is reached by now one of the values is neither a boolean, nor null
        throw new ComparatorException(sprintf('Expected type Boolean for comparison of nodes for NodeTypes '
                . $a->getNodeType()->getName()
                . ' and '
                .$b->getNodeType()->getName()
                .' in property %s', $propertyName, 1482424087));
    }
}