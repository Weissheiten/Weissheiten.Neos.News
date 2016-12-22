<?php
namespace Weissheiten\Neos\News\TypoScript\Eel\FlowQueryOperations;

/*                                                                              *
 * This script belongs to the TYPO3 Flow package "Weissheiten.Neos.News".            *
 * base code from dimaip news extension (https://github.com/sfi-ru/Sfi.News)    *
 *                                                                              */

use TYPO3\Eel\FlowQuery\Operations\AbstractOperation;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\Eel\FlowQuery\FlowQuery;
use TYPO3\TYPO3CR\Domain\Model\Node;
use Weissheiten\Neos\News\Comparator\IComparator;

/**
 * EEL sort() operation to sort Nodes
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
     * @param array $arguments the arguments for this operation
     * @return mixed
     */
    public function evaluate(FlowQuery $flowQuery, array $arguments) {
        if (!isset($arguments[0]) || empty($arguments[0])) {
            throw new \TYPO3\Eel\FlowQuery\FlowQueryException('sort() needs property name by which nodes should be sorted', 1332492263);
        } else {
            $nodes = $flowQuery->getContext();

            $sortByPropertyPath = $arguments[0];
            $sortOrder = 'DESC';
            if (isset($arguments[1]) && !empty($arguments[1]) && in_array($arguments[1], array('ASC', 'DESC'))) {
                $sortOrder = $arguments[1];
            }

            $sortedNodes = array();
            $sortSequence = array();
            $nodesByIdentifier = array();
            /** @var Node $node  */
            foreach ($nodes as $node) {
                $propertyValue = $node->getProperty($sortByPropertyPath);

                if ($propertyValue instanceof \DateTime) {
                    $propertyValue = $propertyValue->getTimestamp();
                }
                $sortSequence[$node->getIdentifier()] = $propertyValue;
                $nodesByIdentifier[$node->getIdentifier()] = $node;
            }
            if ($sortOrder === 'DESC') {
                arsort($sortSequence);
            } else {
                asort($sortSequence);
            }
            foreach ($sortSequence as $nodeIdentifier => $value) {
                $sortedNodes[] = $nodesByIdentifier[$nodeIdentifier];
            }
            $flowQuery->setContext($sortedNodes);
        }
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
