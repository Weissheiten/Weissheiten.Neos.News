<?php
namespace Weissheiten\News\ViewHelpers\Widget\Controller;

/*                                                                        *
 * This script belongs to the package Weissheiten.News    .               *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Fluid\Core\Widget\AbstractWidgetController;
use TYPO3\TYPO3CR\Exception\PageNotFoundException;
use TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository;

/**
 * The widget controller for the Node Paginate Widget
 */
class EventCalendarController extends AbstractWidgetController {
    /**
     * @var \TYPO3\TYPO3CR\Domain\Model\NodeInterface
     */
    protected $locationFilterNode;

    /**
     * @var DateTime
     */
    protected $showMonthYear;

    /**
     * @var \TYPO3\TYPO3CR\Domain\Model\NodeInterface
     */
    protected $startingPoint;

    /**
     * @Flow\Inject
     * @var NodeDataRepository
     */
    protected $nodeDataRepository;

    /**
     * @return void
     */
    protected function initializeAction() {
        $this->locationFilterNode = $this->widgetConfiguration['locationFilterNode'] ?: NULL;
        $this->startingPoint = $this->widgetConfiguration['startingPoint'];
        // set current Month and Year as defaults if nothing is set
        //$this->currentMonth = $this->widgetConfiguration['selectedMonth'] ?: ("n");
        //$this->currentYear = $this->widgetConfiguration['selectedYear'] ?: date("Y");
    }

    /**
     * @param string $showMonthYear
     * @return void
     * @throws \TYPO3\TYPO3CR\Exception\PageNotFoundException
     */
    public function indexAction($showMonthYear = null) {
        if($showMonthYear==null){
            $this->showMonthYear = new \DateTime();
            $this->showMonthYear->modify('first day of this month');
        }
        else{
            $this->showMonthYear = new \DateTime($showMonthYear);
        }


        $nodes = $this->nodeDataRepository->findByParentAndNodeType($this->startingPoint->getPath(), 'Weissheiten.News:Event', $this->startingPoint->getContext()->getWorkspace());

        // get all nodes for the given month/year
        //$nodes = $this->startingPoint->getChildNodes('Weissheiten.News:Event');

        // NodeData Repository => findByParentAndNodeType


        $this->view->assign('contentArguments', array(
            $this->widgetConfiguration['as'] => $nodes
        ));

        $this->view->assign('pagination', $this->buildPagination());


        /*
        $this->currentPage = (integer)$currentPage;
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        } elseif ($this->currentPage > $this->numberOfPages) {
            throw new PageNotFoundException();
        }

        $itemsPerPage = (integer)$this->configuration['itemsPerPage'];
        if ($this->maximumNumberOfNodes > 0 && $this->maximumNumberOfNodes < $itemsPerPage) {
            $itemsPerPage = $this->maximumNumberOfNodes;
        }
        $offset = ($this->currentPage > 1) ? (integer)($itemsPerPage * ($this->currentPage - 1)) : NULL;
        if ($this->parentNode === NULL) {
            $nodes = array_slice($this->nodes, $offset, $itemsPerPage);
        } else {
            $nodes = $this->parentNode->getChildNodes($this->nodeTypeFilter, $itemsPerPage, $offset);
        }*/
/*

*/
        /*
        $this->view->assign('configuration', $this->configuration);
        */
    }

    /**
     * Returns an array with the keys "pages", "current", "numberOfPages", "nextPage" & "previousPage"
     *
     * @return array
     */
    protected function buildPagination() {
        $pagination['currentMonth'] = $this->showMonthYear->format("Y-m-d");
        $pagination['nextMonth'] = $this->showMonthYear->modify('+1 month')->format("Y-m-d");
        $pagination['previousMonth'] = $this->showMonthYear->modify('-2 month')->format("Y-m-d");

        return $pagination;
    }
}
