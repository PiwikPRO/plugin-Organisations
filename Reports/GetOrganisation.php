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
        $this->category       = 'General_Visitors';
        $this->dimension      = new Organisation();
        $this->name           = Piwik::translate('Organisations_Organisation');
        $this->order          = 50;
        $this->widgetTitle    = 'Organisations_Organisation';
        $this->hasGoalMetrics = true;
        //$this->documentation = Piwik::translate('Provider_ProviderReportDocumentation', '<br />');
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
