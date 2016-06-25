<?php
namespace Piwik\Plugins\Organisations;

use Exception;
use Piwik\Archive;
use Piwik\Db;
use Piwik\Network\IPUtils;
use Piwik\Piwik;

class API extends \Piwik\Plugin\API
{
    /**
     * Returns datatable for organisation report
     *
     * @param int $idSite
     * @param string $period
     * @param string $date
     * @param string|boolean $segment
     * @return \Piwik\DataTable|\Piwik\DataTable\Map
     */
    public function getOrganisation($idSite, $period, $date, $segment = false)
    {
        Piwik::checkUserHasViewAccess($idSite);
        $archive   = Archive::build($idSite, $period, $date, $segment);
        $dataTable = $archive->getDataTable(Archiver::ORGANISATIONS_RECORD_NAME);
        $dataTable->filter('GroupBy', array(
            'label',
            function ($label) {
                $model        = new Model();
                $organisation = $model->getOrganisation($label);
                if (!empty($organisation['name'])) {
                    return $organisation['name'];
                }
                return Piwik::translate('General_Unknown');
            }
        ));
        $dataTable->queueFilter('ReplaceColumnNames');
        $dataTable->queueFilter('ReplaceSummaryRowLabel');
        return $dataTable;
    }

    /**
     * Adds an new organisation
     *
     * @param  string $name
     * @param  array $ipRanges
     * @return int
     *
     * @throws Exception  if ip ranges overlap
     * @throws Exception  if no valid ip range was passed
     */
    public function addOrganisation($name, $ipRanges)
    {
        Piwik::checkUserHasSomeAdminAccess();

        $ipRanges = $this->validateIpRanges($ipRanges);

        if (0 === count($ipRanges)) {
            throw new Exception(Piwik::translate('Organisations_ErrorIpRangesEmpty'));
        }

        $idOrg = $this->getModel()->createOrganisation(array(
            'name'     => $name,
            'ipranges' => $ipRanges,
        ));

        return $idOrg;
    }

    /**
     * Updates an existing organisation.
     *
     * @param int $idOrg
     * @param string $name
     * @param array $ipRanges
     *
     * @throws Exception  if ip ranges overlap
     * @throws Exception  if no valid ip range was passed
     */
    public function updateOrganisation($idOrg, $name, $ipRanges)
    {
        Piwik::checkUserHasSomeAdminAccess();

        $ipRanges = $this->validateIpRanges($ipRanges);

        if (0 === count($ipRanges)) {
            throw new Exception(Piwik::translate('Organisations_ErrorIpRangesEmpty'));
        }

        $this->getModel()->updateOrganisation($idOrg, array(
            'name'     => $name,
            'ipranges' => $ipRanges,
        ));
    }

    /**
     * Deletes a specific organisation
     *
     * @param int $idOrg
     */
    public function deleteOrganisation($idOrg)
    {
        Piwik::checkUserHasSomeAdminAccess();
        $this->getModel()->deleteOrganisation($idOrg);
    }

    /**
     * Returns the list of organisations
     *
     * @return array
     */
    public function getAvailableOrganisations()
    {
        Piwik::checkUserHasSomeViewAccess();
        return $this->getModel()->getAll();
    }

    private function getModel()
    {
        return new Model();
    }


    /**
     * Removes invalid IP ranges from list.
     *
     * The overlap checking is not done across organisations!
     *
     * @param  array $ipRanges
     * @return array
     * @throws Exception  if ip ranges overlap
     */
    private function validateIpRanges($ipRanges)
    {
        $filteredIpRanges = array();
        $boundedIpRanges  = array();

        foreach ($ipRanges as $ipRange) {
            $bounds = IPUtils::getIPRangeBounds($ipRange);

            if ($bounds) {
                $filteredIpRanges[] = $ipRange;
                $boundedIpRanges[]  = array(
                    'range'  => $ipRange,  // used for exception message
                    'bounds' => $bounds    // used for bound checking
                );
            }
        }

        // skip check for bound overlapping if less than two ranges available
        if (2 > count($filteredIpRanges)) {
            return $filteredIpRanges;
        }

        usort($boundedIpRanges, function($a, $b) {
            return strcmp($a['bounds'][0], $b['bounds'][0]);
        });

        for ($i = 0; $i < count($boundedIpRanges) - 1; $i += 1) {
            $a = $boundedIpRanges[$i];
            $b = $boundedIpRanges[$i + 1];

            if ($a['bounds'][1] >= $b['bounds'][0]) {
                throw new Exception(
                    Piwik::translate(
                        'Organisations_ErrorIpRangesOverlap',
                        array($b['range'], $a['range'])
                    )
                );
            }
        }

        return $filteredIpRanges;
    }
}
