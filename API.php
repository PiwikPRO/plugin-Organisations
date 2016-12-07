<?php
/*
 *  Piwik - free/libre analytics platform

 *  Piwik is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 *  Piwik is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.

 *  @link http://piwik.pro
 *  @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

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

        $ipRanges = $this->validateIpRanges($ipRanges, null);

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

        $ipRanges = $this->validateIpRanges($ipRanges, $ignoreIdOrg = $idOrg);

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
     * @param  int   $ignoreIdOrg
     * @return array
     * @throws Exception  if ip ranges overlap
     */
    private function validateIpRanges($ipRanges, $ignoreIdOrg = null)
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

        usort($boundedIpRanges, function($a, $b) {
            return strcmp($a['bounds'][0], $b['bounds'][0]);
        });

        $this->checkForInternalOverlap($boundedIpRanges);
        $this->checkForGlobalOverlap($boundedIpRanges, $ignoreIdOrg);

        return $filteredIpRanges;
    }

    /**
     * Checks if the ip ranges of an organsation overlap with a different one.
     *
     * @param array $ipRanges
     * @param int   $ignoreIdOrg
     *
     * @throws Exception  if ip ranges overlap
     */
    private function checkForGlobalOverlap($ipRanges, $ignoreIdOrg = null)
    {
        if (0 === count($ipRanges)) {
            // skip check if there is ip range
            return;
        }

        $orgLowest  = $ipRanges[0]['bounds'][0];
        $orgHighest = $ipRanges[count($ipRanges) - 1]['bounds'][1];

        $organisations  = $this->getAvailableOrganisations();
        $globalIpRanges = array();

        if (0 === count($organisations)) {
            // skip check if there are no other organisations
            return;
        }

        foreach ($organisations as $organisation) {
            if (null !== $ignoreIdOrg && $ignoreIdOrg == $organisation['idorg']) {
                // ignore "own" organisation on update
                continue;
            }

            foreach ($organisation['ipranges'] as $ipRange) {
                $bounds = IPUtils::getIPRangeBounds($ipRange);

                // completely below organisation range
                if (0 < strcmp($orgLowest, $bounds[1])) {
                    continue;
                }

                // completely above organisation range
                if (0 > strcmp($orgHighest, $bounds[0])) {
                    continue;
                }

                $globalIpRanges[] = array(
                    'name'   => $organisation['name'],  // used for exception message
                    'range'  => $ipRange,               // used for exception message
                    'bounds' => $bounds                 // used for bound checking
                );
            }
        }

        // skip check if no overlap possible
        if (0 === count($globalIpRanges)) {
            return;
        }

        foreach ($ipRanges as $ipRange) {
            foreach ($globalIpRanges as $globalIpRange) {
                if ((0 <= strcmp($ipRange['bounds'][0], $globalIpRange['bounds'][0]) && 0 >= strcmp($ipRange['bounds'][0], $globalIpRange['bounds'][1])) ||
                    (0 <= strcmp($ipRange['bounds'][1], $globalIpRange['bounds'][0]) && 0 >= strcmp($ipRange['bounds'][1], $globalIpRange['bounds'][1])) ||
                    (0 >= strcmp($ipRange['bounds'][0], $globalIpRange['bounds'][0]) && 0 <= strcmp($ipRange['bounds'][1], $globalIpRange['bounds'][1]))) {
                    throw new Exception(
                        Piwik::translate(
                            'Organisations_ErrorIpRangesOverlapGlobal',
                            array($ipRange['range'], $globalIpRange['range'], $globalIpRange['name'])
                        )
                    );
                }
            }
        }
    }

    /**
     * Checks if the ip ranges inside one organisation overlap.
     *
     * @param array $ipRanges
     *
     * @throws Exception  if ip ranges overlap
     */
    private function checkForInternalOverlap($ipRanges)
    {
        if (1 >= count($ipRanges)) {
            // skip check if there is not more than one ip ranges
            return;
        }

        for ($i = 0; $i < count($ipRanges) - 1; $i += 1) {
            $a = $ipRanges[$i];
            $b = $ipRanges[$i + 1];

            if (0 <= strcmp($a['bounds'][1], $b['bounds'][0])) {
                throw new Exception(
                    Piwik::translate(
                        'Organisations_ErrorIpRangesOverlap',
                        array($b['range'], $a['range'])
                    )
                );
            }
        }
    }
}
