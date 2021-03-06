<?php
namespace Weissheiten\Neos\News\ViewHelpers\Widget;

/*                                                                        *
 * This script belongs to the Weissheiten.Neos.News package                    *
 *                                                                        */

use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\Core\Widget\AbstractWidgetViewHelper;
use Neos\ContentRepository\Domain\Model\NodeInterface;

/**
 * This ViewHelper renders a Calendar for nodes (events)
 *
 * = Examples =
 *
 * <code title="specifying the parent node">
 * <typo3cr:widget.paginate parentNode="{parentNode}" as="paginatedNodes" configuration="{itemsPerPage: 5}">
 *   // use {paginatedNodes} inside a <f:for> loop.
 * </typo3cr:widget.paginate>
 * </code>
 *
 * <code title="specifying the nodes explicitly">
 * <typo3cr:widget.paginate nodes="{myNodes}" as="paginatedNodes" configuration="{itemsPerPage: 5}">
 *   // use {paginatedNodes} inside a <f:for> loop.
 * </typo3cr:widget.paginate>
 * </code>
 *
 * <code title="full configuration">
 * <typo3cr:widget.paginate parentNode="{parentNode}" as="paginatedNodes" nodeTypeFilter="Neos.NodeTypes:Page" configuration="{itemsPerPage: 5, insertAbove: 1, insertBelow: 0, maximumNumberOfLinks: 10, maximumNumberOfNodes: 350}">
 *   // use {paginatedNodes} inside a <f:for> loop.
 * </typo3cr:widget.paginate>
 * </code>
 *
 * @api
 */
class EventCalendarViewHelper extends AbstractWidgetViewHelper {

    /**
     * @Flow\Inject
     * @var \Weissheiten\Neos\News\ViewHelpers\Widget\Controller\EventCalendarController
     */
    protected $controller;

    /**
     * Render this view helper
     *
     * @param string $as Variable name for the result set
     * @param \Neos\Eel\FlowQuery\FlowQuery $calendarEntries The node which must occur as a location reference in the event to be included in the record set
     * @param \Neos\ContentRepository\Domain\Model\NodeInterface $startingPoint Starting point for traversing the tree in search of fitting nodes
     * @return string
     */
    public function render($as, $calendarEntries = NULL, $locationFilterNode = NULL) {
        $response = $this->initiateSubRequest();
        return $response->getContent();
    }
}
