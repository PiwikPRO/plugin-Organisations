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
        return Piwik::translate('General_Unknown');
    }

    public function getOrganisationName()
    {
        // @todo use model to return pretty org name
        return $this->getOrganisation();
    }
}