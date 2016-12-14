<?php
/**
 * Piwik PRO - Premium functionality and enterprise-level support for Piwik Analytics
 *
 * @link http://piwik.pro
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\Organisations;

use Piwik\API\Request;
use Piwik\Common;
use Piwik\Piwik;
use Piwik\SettingsPiwik;
use Piwik\Site;
use Piwik\Translation\Translator;
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
        $view = $this->getLastUnitGraph($this->pluginName, __FUNCTION__, 'Organisations.getOrganisation');

        $view->config->add_total_row = false;

        if (SettingsPiwik::isUniqueVisitorsEnabled(Common::getRequestVar('period', false))) {
            $selectable = array('nb_visits', 'nb_uniq_visitors', 'nb_users', 'nb_actions');
        } else {
            $selectable = array('nb_visits', 'nb_actions');
        }
        $view->config->selectable_columns = $selectable;

        // configure displayed columns
        if (empty($columns)) {
            $columns = Common::getRequestVar('columns', false);
            if (false !== $columns) {
                $columns = Piwik::getArrayFromApiParameter($columns);
            }
        }
        if (false !== $columns) {
            $columns = !is_array($columns) ? array($columns) : $columns;
        }

        if (!empty($columns)) {
            $view->config->columns_to_display = $columns;
        } elseif (empty($view->config->columns_to_display) && !empty($defaultColumns)) {
            $view->config->columns_to_display = $defaultColumns;
        }

        // configure displayed rows
        $visibleRows = Common::getRequestVar('rows', false);
        if ($visibleRows !== false) {
            // this happens when the row picker has been used
            $visibleRows = Piwik::getArrayFromApiParameter($visibleRows);
            $view->config->custom_parameters['organisation'] = false;
        }

        $view->config->row_picker_match_rows_by = 'label';
        $view->config->rows_to_display = $visibleRows;

        return $this->renderView($view);
    }

    public function adminIndex()
    {
        Piwik::checkUserHasSomeAdminAccess();

        $view = new View('@Organisations/admin');
        $view->organisations = Request::processRequest('Organisations.getAvailableOrganisations');

        $this->setBasicVariablesView($view);
        $this->setBasicVariablesAdminView($view);

        return $view->render();
    }
}

