<?php
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
