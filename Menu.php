<?php
/**
 * Piwik PRO - Premium functionality and enterprise-level support for Piwik Analytics
 *
 * @link http://piwik.pro
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\Organisations;

use Piwik\Menu\MenuAdmin;
use Piwik\Menu\MenuReporting;

class Menu extends \Piwik\Plugin\Menu
{
    public function configureReportingMenu(MenuReporting $menu)
    {
        $menu->addVisitorsItem('Organisations_Organisations', $this->urlForAction('index'), 55);
    }

    public function configureAdminMenu(MenuAdmin $menu)
    {
        $menu->addManageItem('Organisations_Organisations',  $this->urlForAction('adminIndex'));
    }
}
