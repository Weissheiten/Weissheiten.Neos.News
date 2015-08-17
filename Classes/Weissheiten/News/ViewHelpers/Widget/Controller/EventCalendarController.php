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
use TYPO3\Eel\FlowQuery\FlowQuery;
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
     * @var array<\TYPO3\TYPO3CR\Domain\Model\NodeInterface>
     */
    protected $calendarEntries;

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
        $this->calendarEntries = $this->widgetConfiguration['calendarEntries'];
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
            // set the time to 0,0,0, to be able to compare with other dates
            $this->showMonthYear->modify('first day of this month')->setTime(0, 0, 0);
        }
        else{
            $this->showMonthYear = new \DateTime($showMonthYear);
        }

        // This is suboptimal performance wise for a lot of nodes - at the moment there is no nice plain Neos way to filter
        // in the future this has to be changed to elastic search or a similar approach
        //$q = new FlowQuery(array($this->startingPoint));
        //$nodes = $q->children("[instanceof 'Weissheiten.News:Event']")->get();
        $rnodes = array();
        // filter the nodes according to the date

        foreach($this->calendarEntries as $node){
            if(!is_null($node->getProperty('eventDate'))){
                $ndate = clone $node->getProperty('eventDate');
                $ndate->modify('first day of this month')->setTime(0, 0, 0);

                if($ndate==$this->showMonthYear){
                    $rnodes[] = $node;
                }
            }
            else{
                // Entries without an Event Date are considered recurring and additional info has to be given by the editor in the categories section
                $rend_nodes[] = $node;
            }
        }

        // merge all hits matching the month with all arrays that have NO date and are therefore weekly or monthly
        $rnodes = array_merge($rnodes,$rend_nodes);

        $this->view->assign('contentArguments', array(
            $this->widgetConfiguration['as'] => $rnodes)
        );

        $this->view->assign('pagination', $this->buildPagination());
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
