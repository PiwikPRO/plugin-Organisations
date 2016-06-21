<?php
namespace Piwik\Plugins\Organisations;

use Piwik\ArchiveProcessor;
use Piwik\Db;

class Organisations extends \Piwik\Plugin
{
    /**
     * @see Piwik\Plugin::registerEvents
     */
    public function registerEvents()
    {
        return array(
            'Live.getAllVisitorDetails' => 'extendVisitorDetails'
        );
    }

    public function extendVisitorDetails(&$visitor, $details)
    {
        $instance = new Visitor($details);

        $visitor['organisation']     = $instance->getOrganisation();
        $visitor['organisationName'] = $instance->getOrganisationName();
    }
}
