<?php
namespace Weissheiten\Neos\News\ViewHelpers\Widget\Controller;

/*                                                                        *
 * This script belongs to the package Weissheiten.Neos.   .               *
 *                                                                        *
 *                                                                        */

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;
use Neos\FluidAdaptor\Core\Widget\AbstractWidgetController;
use Neos\ContentRepository\Exception\PageNotFoundException;
use Neos\ContentRepository\Domain\Repository\NodeDataRepository;
use Neos\Eel\FlowQuery\FlowQuery;
/**
 * The widget controller for the Node Paginate Widget
 */
class EventCalendarController extends AbstractWidgetController {
    /**
     * @var \Neos\ContentRepository\Domain\Model\NodeInterface
     */
    protected $locationFilterNode;

    /**
     * @var DateTime
     */
    protected $showMonthYear;

    /**
     * @var array<\Neos\ContentRepository\Domain\Model\NodeInterface>
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
     * @throws \Neos\ContentRepository\Exception\PageNotFoundException
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
        //$nodes = $q->children("[instanceof 'Weissheiten.Neos.News:Event']")->get();
        $rnodes = array();
        $recurringNodes = array();
        // filter the nodes according to the date

        foreach($this->calendarEntries as $node){
            if(!is_null($node->getProperty('eventDate'))){
                // this is a one time event
                if(is_null($node->getProperty('eventEnd'))){
                    $ndate = clone $node->getProperty('eventDate');
                    $ndate->modify('first day of this month')->setTime(0, 0, 0);

                    if($ndate==$this->showMonthYear){
                        $rnodes[] = $node;
                    }
                }
                else{
                    $eventDate = $node->getProperty('eventDate')->modify('first day of this month')->setTime(0, 0, 0);;
                    $eventEnd = $node->getProperty('eventEnd')->modify('first day of this month')->setTime(0, 0, 0);;
                    $current = $this->showMonthYear;

                    // if the Event timing is inside the window - show it
                    if($current>=$eventDate && $current<=$eventEnd){
                        $recurringNodes[] = $node;
                    }
                }
            }
            else{
                // Ignore this node - someone forgot to set an event date and the backend didn't catch it
                // Entries without an Event Date are considered recurring and additional info has to be given by the editor in the categories section
                //$rend_nodes[] = $node;
            }
        }

        // merge all hits matching the month with all arrays that have NO date and are therefore weekly or monthly
        $rnodes = array_merge($rnodes,$recurringNodes);

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
