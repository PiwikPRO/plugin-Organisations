<?php
namespace Piwik\Plugins\Organisations\Columns;

use Piwik\Network\IP;
use Piwik\Network\IPUtils;
use Piwik\Piwik;
use Piwik\Plugin\Dimension\VisitDimension;
use Piwik\Plugins\Organisations\Model;
use Piwik\Plugins\Resolution\Segment;
use Piwik\Tracker\Action;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;
use Piwik\Plugins\PrivacyManager\Config as PrivacyManagerConfig;

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
        $ip = $this->getIpAddress($visitor->getVisitorColumn('location_ip'), $request);

        if (empty($ip)) {
            return 0;
        }

        $model    = new Model();
        $ipRanges = $model->getIpRangeMapping();

        $ip = IP::fromStringIP($ip);

        foreach ($ipRanges as $ipRange => $orgId) {
            if ($ip->isInRange($ipRange)) {
                return $orgId;
            }
        }

        return 0;
    }

    /**
     * @param Request $request
     * @param Visitor $visitor
     * @param Action|null $action
     * @return mixed
     */
    public function onAnyGoalConversion(Request $request, Visitor $visitor, $action)
    {
        return $visitor->getVisitorColumn('organisation');
    }

    public function getName()
    {
        return Piwik::translate('Organisations_Organisation');
    }

    private function getIpAddress($anonymizedIp, \Piwik\Tracker\Request $request)
    {
        $privacyConfig = new PrivacyManagerConfig();

        $ip = $request->getIp();

        if ($privacyConfig->useAnonymizedIpForVisitEnrichment) {
            $ip = $anonymizedIp;
        }

        $ipAddress = IPUtils::binaryToStringIP($ip);

        return $ipAddress;
    }
}