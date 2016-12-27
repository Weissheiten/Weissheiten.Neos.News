<?php
namespace Weissheiten\Neos\News\TypoScript\Eel\FlowQueryOperations;

/*                                                                              *
 * This script belongs to the TYPO3 Flow package "Weissheiten.Neos.News".       *
 * base code from dimaip news extension (https://github.com/sfi-ru/Sfi.News)    *
 *                                                                              */

use TYPO3\Eel\FlowQuery\Operations\AbstractOperation;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\Eel\FlowQuery\FlowQuery;
use TYPO3\TYPO3CR\Domain\Model\Node;
use Weissheiten\Neos\News\Comparator\BooleanComparator;
use Weissheiten\Neos\News\Comparator\DateComparator;
use Weissheiten\Neos\News\Comparator\IComparator;

/**
 * EEL sort() operation to sort Nodes by multiple properties (subsort), for use-cases where more than one sort criteria is needed
 */
class SortByMultipleOperation extends AbstractOperation {
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    static protected $shortName = 'sortByMultiple';

    /**
     * {@inheritdoc}
     *
     * @var integer
     */
    static protected $priority = 100;

    /**
     * @param $comparatorArray
     * @return \Closure Closure for usort comparison
     */
    private function build_sorter($comparatorArray){
        return function($a, $b) use ($comparatorArray){
            foreach($comparatorArray as $comparator){
                $comparerclass = $comparator['class'];

                /* @var IComparator $comparerclass */
                $result = $comparerclass->execute($a,$b,$comparator['property'], $comparator['order']);

                if($result<>0){
                    return $result;
                }
            }
            return 0;
        };
    }

    /**
     * {@inheritdoc}
     *
     * @param FlowQuery $flowQuery the FlowQuery object
     * @param array $arguments the arguments for this operation, should always come in couples of propertyname and order
     * @return mixed
     */
    public function evaluate(FlowQuery $flowQuery, array $arguments) {
        // check if there are at least 4 arguments, if not give advice to use "classic" sort
        if(count($arguments)<4){
            throw new \TYPO3\Eel\FlowQuery\FlowQueryException('sortByMultiple() needs at least 4 properties (2 node-properties and their sort order), if you only need to sort by a single property use "sort" instead', 1482825509);
        }

        // check arguments and build search array
        for($i=0; $i<count($arguments);$i+=2){
            $property = $arguments[$i];
            $sortdirection = strtolower($arguments[$i+1]);

            if($property===null || $sortdirection===null || ($sortdirection!=='asc' && $sortdirection!=='desc')){
                throw new \TYPO3\Eel\FlowQuery\FlowQueryException('sortByMultiple() expects couples of "property Name" and "sort direction" (ASC or DESC)', 1482825940);
            }
        }

        $nodes = $flowQuery->getContext();

        if(count($nodes)>0){
            $comparatorArray = array();

            for($i=0; $i<count($arguments);$i+=2){
                /** @var Node $node */
                $node = $nodes[0];
                $property = $arguments[$i];
                $sortdirection = strtolower($arguments[$i+1]);

                $propertyType = $node->getNodeType()->getPropertyType($property);

                $order = (strtolower($sortdirection)==='asc' ? true : false);

                switch ($propertyType){
                    case "DateTime":
                        $comparatorArray[] = array(
                            'class' => new DateComparator(),
                            'property' => $property,
                            'order' => $order
                        );
                        break;
                    case "boolean":
                        $comparatorArray[] = array(
                            'class' => new BooleanComparator(),
                            'property' => $property,
                            'order' => $order
                        );
                        break;
                    default:
                        throw new \TYPO3\Eel\FlowQuery\FlowQueryException('sortByMultiple(): the resolved propertytype (' . $propertyType .  ') of property ' . $arguments[$i] . ' is not supported at this time', 1482829065);
                }
            }
            usort($nodes,$this->build_sorter($comparatorArray));
        }

        $flowQuery->setContext($nodes);
    }

    /**
     * {@inheritdoc}
     *
     * Can handle TYPO3CR NodeTypes, also works with an empty context
     *
     * @param array (or array-like object) $context onto which this operation should be applied
     * @return boolean TRUE if the operation can be applied onto the $context, FALSE otherwise
     */
    public function canEvaluate($context) {
        if (count($context) === 0) {
            return true;
        }

        foreach ($context as $contextNode) {
            if (!$contextNode instanceof NodeInterface) {
                return false;
            }
        }
        return true;
    }
}
