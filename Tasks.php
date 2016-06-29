<?php
namespace Piwik\Plugins\Organisations;

class Tasks extends \Piwik\Plugin\Tasks
{
    public function schedule()
    {
        $this->daily('clearOrganisationCache'); // clear cache at least once a day if required
    }

    /**
     * Triggers the clearing of organisation cache
     */
    public function clearOrganisationCache()
    {
        $model = new Model();
        $model->clearTrackerCacheIfRequired();
    }
}