<?php
namespace Piwik\Plugins\Organisations;

use Piwik\Common;
use Piwik\Piwik;
use Piwik\Site;
use Piwik\View;

/**
 *
 */
class Controller extends \Piwik\Plugin\ControllerAdmin
{
    public function index()
    {
        $view = new View('@Organisations/index');
        $view->graphEvolution = $this->getEvolutionGraph(array(), array('nb_visits'));
        $view->dataTableOrganisations = $this->renderReport('getOrganisation');
        return $view->render();
    }

    public function getEvolutionGraph(array $columns = array(), array $defaultColumns = array())
    {
        if (empty($columns)) {
            $columns = Common::getRequestVar('columns', false);
            if (false !== $columns) {
                $columns = Piwik::getArrayFromApiParameter($columns);
            }
        }

        $selectableColumns = array(
            // columns from VisitsSummary.get
            'nb_visits',
            'nb_uniq_visitors',
            'nb_users',
            'avg_time_on_site',
            'bounce_rate',
            'nb_actions_per_visit',
            'max_actions',
            'nb_visits_converted',
            // columns from Actions.get
            'nb_pageviews',
            'nb_uniq_pageviews',
            'nb_downloads',
            'nb_uniq_downloads',
            'nb_outlinks',
            'nb_uniq_outlinks',
            'avg_time_generation'
        );

        $idSite = Common::getRequestVar('idSite');
        $displaySiteSearch = Site::isSiteSearchEnabledFor($idSite);

        if ($displaySiteSearch) {
            $selectableColumns[] = 'nb_searches';
            $selectableColumns[] = 'nb_keywords';
        }
        $view = $this->getLastUnitGraphAcrossPlugins($this->pluginName, __FUNCTION__, $columns,
            $selectableColumns, false, 'Organisations.getOrganisation');

        if (empty($view->config->columns_to_display) && !empty($defaultColumns)) {
            $view->config->columns_to_display = $defaultColumns;
        }

        return $this->renderView($view);
    }

    public function adminIndex()
    {
        Piwik::checkUserHasSomeAdminAccess();
        $view = new View('@Organisations/admin');
        $this->setBasicVariablesView($view);
        $this->setBasicVariablesAdminView($view);
        return $view->render();
    }
}

