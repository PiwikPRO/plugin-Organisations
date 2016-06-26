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
            'Tracker.setTrackerCacheGeneral'  => 'setTrackerCacheGeneral',
            'Live.getAllVisitorDetails' => 'extendVisitorDetails'
        );
    }


    public function setTrackerCacheGeneral(&$cacheContent)
    {
        $model = new Model();
        return $model->setTrackerCache($cacheContent);
    }

    public function extendVisitorDetails(&$visitor, $details)
    {
        $instance = new Visitor($details);

        $visitor['organisation']     = $instance->getOrganisation();
        $visitor['organisationName'] = $instance->getOrganisationName();
    }

    public function isTrackerPlugin()
    {
        return true;
    }

    public function install()
    {
        Model::install();
    }
}
