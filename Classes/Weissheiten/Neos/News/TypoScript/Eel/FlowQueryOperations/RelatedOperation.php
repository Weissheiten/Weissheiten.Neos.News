<?php
namespace Weissheiten\Neos\News\TypoScript\Eel\FlowQueryOperations;

/*                                                                              *
 * This script belongs to the TYPO3 Flow package "Weissheiten.Neos.News".              *
 * Used to get a list of related Entries                                        *
 *                                                                              */

use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Eel\FlowQuery\Operations\AbstractOperation;
use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\Node;
use Neos\ContentRepository\Domain\Model\NodeInterface;
/**
 * EEL related() operation to find related Nodes
 */
class RelatedOperation extends AbstractOperation {
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    static protected $shortName = 'related';

    /**
     * {@inheritdoc}
     *
     * @var integer
     */
    static protected $priority = 100;

    /**
     * {@inheritdoc}
     *
     * We can only handle TYPO3CR Nodes.
     *
     * @param mixed $context
     * @return boolean
     */
    public function canEvaluate($context) {
        return (isset($context[0]) && ($context[0] instanceof NodeInterface));
    }

    /**
     * {@inheritdoc}
     *
     * @param FlowQuery $flowQuery the FlowQuery object
     * @param array $arguments the arguments for this operation
     * @return mixed
     */
    public function evaluate(FlowQuery $flowQuery, array $arguments)
    {
        if (!isset($arguments[0]) || empty($arguments[0])) {
            throw new \Neos\Eel\FlowQuery\FlowQueryException('related() needs property name by which nodes should be matched', 1332492263);
        } else {
            $nodes = $flowQuery->getContext();
            // Property that contains the reference
            $lookupProperty = $arguments[0];
            // The last element of the nodes array is the one for which the type should be compared
            $comparer = array_pop($nodes);
            $identifier = $comparer->getIdentifier();

            // Define an output array
            $relatedNodes = array();

            /** @var Node $node */
            foreach ($nodes as $node) {
                if($node->hasProperty($lookupProperty)) {
                    if($this->containsMatchingReference($node->getProperty($lookupProperty),$identifier)){
                        $relatedNodes[] = $node;
                    }
                }
            }
            $flowQuery->setContext($relatedNodes);
        }
    }

    /**
     * Checks a parameters identifier against the identifier also passed to the function
     * Parameter can be a node or a nodearray
     *
     * @param $node single node or nodes array
     * @param $identifier node identifier to match
     * @return bool matching identifiers or at least one matching identifier in the nodes array
     */
    private function containsMatchingReference($node, $identifier) {
        // NodeProperty is of type "Reference"
        if($node instanceof NodeInterface) {
            if($identifier === $node->getIdentifier()) {
                return true;
            }
        }
        // NodeProperty is of type "References" - search is done for at least one matching node with a matching identifier
        elseif(is_array($node)){
            foreach($node as $subnode){
                if($subnode instanceof NodeInterface) {
                    if($identifier === $subnode->getIdentifier()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

}
