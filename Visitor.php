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