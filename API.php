<?php
namespace Piwik\Plugins\Organisations;

use Piwik\Db;
use Piwik\Piwik;

class API extends \Piwik\Plugin\API
{
    /**
     * Adds an new organisation
     *
     * @param  string $name
     * @param  array  $ipRanges
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
     * @param int     $idOrg
     * @param string  $name
     * @param array   $ipRanges
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
     * Returns a specific organisation
     *
     * @param int $idOrg
     * @return array
     */
    public function getOrganisation($idOrg)
    {
        Piwik::checkUserHasSomeViewAccess();
        return $this->getModel()->getOrganisation($idOrg);
    }

    /**
     * Returns the list of organisations
     *
     * @return array
     */
    public function getOrganisations()
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
        // @todo iterate through IP ranges and filter invalid ones
        return $ipRanges;
    }
}
