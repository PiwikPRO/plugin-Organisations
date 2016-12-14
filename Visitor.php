<?php
/**
 * Piwik PRO - Premium functionality and enterprise-level support for Piwik Analytics
 *
 * @link http://piwik.pro
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */


namespace Piwik\Plugins\Organisations;

use Piwik\Piwik;

class Visitor
{
    private $details = array();

    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Returns organisation id
     *
     * @return array|int
     */
    public function getOrganisation()
    {
        if (isset($this->details['organisation'])) {
            return $this->details['organisation'];
        }
        return 0;
    }

    /**
     * Returns organisation name or 'Unknown' if none is found
     *
     * @return string
     */
    public function getOrganisationName()
    {
        $orgId = $this->getOrganisation();

        if (is_numeric($orgId)) {
            $model        = new Model();
            $organisation = $model->getOrganisation($orgId);
            if (!empty($organisation['name'])) {
                return $organisation['name'];
            }
        }

        return Piwik::translate('General_Unknown');
    }
}