<?php
namespace Piwik\Plugins\Organisations;

use Piwik\Piwik;

class Visitor
{
    private $details = array();

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function getOrganisation()
    {
        if (isset($this->details['location_provider'])) {
            return $this->details['location_provider'];
        }
        return 0;
    }

    public function getOrganisationName()
    {
        $orgId = $this->getOrganisation();

        if (is_numeric($orgId)) {
            $model        = new Model();
            $organisation = $model->getOrganisation($orgId);
            return $organisation['name'];
        }

        return Piwik::translate('General_Unknown');
    }
}