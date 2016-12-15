<?php
/**
 * Piwik PRO - Premium functionality and enterprise-level support for Piwik Analytics
 *
 * @link http://piwik.pro
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */


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
