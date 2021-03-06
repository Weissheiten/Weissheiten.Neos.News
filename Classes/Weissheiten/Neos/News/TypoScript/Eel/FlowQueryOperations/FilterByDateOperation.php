<?php
namespace Weissheiten\Neos\News\TypoScript\Eel\FlowQueryOperations;

/*                                                                        *
 * This script is used from the TYPO3 Flow package "Sfi.News".            *
 *                                                                        *
 *                                                                        */

use Neos\Eel\FlowQuery\Operations\AbstractOperation;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\Node;
use Neos\ContentRepository\Domain\Model\NodeInterface;

/**
 * EEL sort() operation to sort Nodes
 */
class FilterByDateOperation extends AbstractOperation {
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    static protected $shortName = 'filterByDate';

    /**
     * {@inheritdoc}
     *
     * @var integer
     */
    static protected $priority = 100;

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

    /**
     * {@inheritdoc}
     *
     * @param FlowQuery $flowQuery the FlowQuery object
     * @param array $arguments the arguments for this operation.
     * First argument is property to filter by, must be DateTime.
     * Second is Date operand, must be DateTime object.
     * And third is a compare operator: '<' or '>', '>' by default
     * @return mixed
     */
    public function evaluate(FlowQuery $flowQuery, array $arguments) {
        if (!isset($arguments[0]) || empty($arguments[0])) {
            throw new \Neos\Eel\FlowQuery\FlowQueryException('filterByDate() needs property name by which nodes should be filtered', 1332492263);
        } else if (!isset($arguments[1]) || empty($arguments[1])) {
            throw new \Neos\Eel\FlowQuery\FlowQueryException('filterByDate() needs date value by which nodes should be filtered', 1332493263);
        } else {
            $nodes = $flowQuery->getContext();
            $filterByPropertyPath = $arguments[0];
            $date = $arguments[1];
            $compareOperator = '>';
            if (isset($arguments[2]) && !empty($arguments[2]) && in_array($arguments[2], array('<', '>'))) {
                $compareOperator = $arguments[2];
            }

            $nullIsMax = false;
            // defines if a null value in the date should be considered as max
            if(isset($arguments[3]) && $arguments[3]==true){
                $nullIsMax = true;
            }

            $filteredNodes = array();
            /** @var Node $node  */
            foreach ($nodes as $node) {
                $propertyValue = $node->getProperty($filterByPropertyPath);

                // consider a null value as max, e.g.: archiving purposes ("never archive this")
                if($propertyValue===null && $nullIsMax){
                    $propertyValue = new \DateTime('3000-12-31');
                }

                if ($compareOperator == '>') {
                    if ($propertyValue > $date) {
                        $filteredNodes[] = $node;
                    }
                }
                if ($compareOperator == '<') {
                    if ($propertyValue < $date) {
                        $filteredNodes[] = $node;
                    }
                }
            }
            $flowQuery->setContext($filteredNodes);
        }
    }
}
