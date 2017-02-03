<?php
/**
 * Piwik PRO - Premium functionality and enterprise-level support for Piwik Analytics
 *
 * @link http://piwik.pro
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */


namespace Piwik\Plugins\Organisations\Columns;

use Piwik\Network\IP;
use Piwik\Network\IPUtils;
use Piwik\Piwik;
use Piwik\Plugin\Dimension\VisitDimension;
use Piwik\Plugins\Organisations\Exception\InvalidOrganisationSegmentException;
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

    /**
     * @throws InvalidOrganisationSegmentException
     */
    protected function configureSegments()
    {
        $segment = new Segment();
        $segment->setSegment('organisation');
        $segment->setName('Organisations_Organisation');

        $model = new Model();
        $organisationNames = array(
            0 => Piwik::translate('General_Unknown')
        );
        $organisations = $model->getAll();
        foreach ($organisations as $organisation) {
            $organisationNames[$organisation['idorg']] = $organisation['name'];
        }

        $segment->setAcceptedValues(implode(', ', $organisationNames));
        $segment->setSuggestedValuesCallback(function() use ($organisationNames) { return $organisationNames; });
        $segment->setSqlFilter(function ($org) use ($organisationNames) {
            if ($org == Piwik::translate('General_Unknown')) {
                return 0;
            }
            $index = array_search(trim(urldecode($org)), $organisationNames);
            if ($index === false) {
                throw new InvalidOrganisationSegmentException($organisationNames);
            }
            return $index;
        });

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
        $model    = new Model();

        // check for forced organisation id in request
        $rawRequestParams = $request->getRawParams();
        if (isset($rawRequestParams['idorg'])) {
            $org = $model->getOrganisation($rawRequestParams['idorg']);
            if (!empty($org)) {
                return $rawRequestParams['idorg'];
            }
        }

        $ip = $this->getIpAddress($visitor->getVisitorColumn('location_ip'), $request);

        if (empty($ip)) {
            return 0;
        }

        return $model->getOrganisationFromIp($ip);

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
