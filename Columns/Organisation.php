<?php
namespace Piwik\Plugins\Organisations\Columns;

use Piwik\Piwik;
use Piwik\Plugin\Dimension\VisitDimension;
use Piwik\Plugins\Resolution\Segment;
use Piwik\Tracker\Action;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;

class Organisation extends VisitDimension
{
    protected $columnName = 'organisation';
    protected $columnType = 'SMALLINT(5) NOT NULL';

    protected function configureSegments()
    {
        $segment = new Segment();
        $segment->setSegment('organisation');
        $segment->setName('Organisations_Organisation');
        // @todo Set accepted values based on the save organisations
        $this->addSegment($segment);
    }

    /**
     * @param Request $request
     * @param Visitor $visitor
     * @param Action|null $action
     * @return mixed
     */
    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
        // @todo identify organsiation based on IP and return the id
        return '';
    }

    public function getName()
    {
        return Piwik::translate('Organisations_Organisation');
    }
}