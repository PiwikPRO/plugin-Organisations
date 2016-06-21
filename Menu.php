<?php
namespace Piwik\Plugins\Organisations;

use Piwik\Menu\MenuReporting;

class Menu extends \Piwik\Plugin\Menu
{
    public function configureReportingMenu(MenuReporting $menu)
    {
        $menu->addVisitorsItem('Organisations_Organisation', $this->urlForAction('index'), 55);
    }
}
