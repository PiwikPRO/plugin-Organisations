<?php
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
        $this->category      = 'General_Visitors';
        $this->dimension     = new Organisation();
        $this->name          = Piwik::translate('Organisations_Organisation');
        //$this->documentation = Piwik::translate('Provider_ProviderReportDocumentation', '<br />');
        $this->order = 50;
        $this->widgetTitle  = 'Organisations_Organisation';
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
