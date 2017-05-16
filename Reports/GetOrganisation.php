<?php
/**
 * Piwik PRO - Premium functionality and enterprise-level support for Piwik Analytics
 *
 * @link http://piwik.pro
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\Organisations\Reports;

use Piwik\Piwik;
use Piwik\Plugin\Report;
use Piwik\Plugin\ViewDataTable;
use Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph\Pie;
use Piwik\Plugins\Organisations\Columns\Organisation;

class GetOrganisation extends Report
{
    protected function init()
    {
        $this->categoryId     = 'General_Visitors';
        $this->subcategoryId  = 'Organisations_Organisation';
        $this->dimension      = new Organisation();
        $this->name           = Piwik::translate('Organisations_Organisation');
        $this->order          = 50;
        $this->hasGoalMetrics = true;
    }

    public function getDefaultTypeViewDataTable()
    {
        return Pie::ID;
    }

    public function configureView(ViewDataTable $view)
    {
        $view->config->addTranslation('label', $this->dimension->getName());
    }
}
