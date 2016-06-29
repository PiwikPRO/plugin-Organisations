<?php
namespace Piwik\Plugins\Organisations;

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
     */
    public function addOrganisation($name, $ipRanges)
    {
        Piwik::checkUserHasSomeAdminAccess();

        $ipRanges = $this->validateIpRanges($ipRanges);

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
     */
    public function updateOrganisation($idOrg, $name, $ipRanges)
    {
        Piwik::checkUserHasSomeAdminAccess();

        $ipRanges = $this->validateIpRanges($ipRanges);

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

    private static function validateIpRanges($ipRanges)
    {
        $filteredIpRanges = array();

        foreach ($ipRanges as $ipRange) {
            if (IPUtils::getIPRangeBounds($ipRange)) {
                $filteredIpRanges[] = $ipRange;
            }
        }

        return $filteredIpRanges;
    }
}
