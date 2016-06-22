<?php
namespace Piwik\Plugins\Organisations;

use Piwik\View;

/**
 *
 */
class Controller extends \Piwik\Plugin\Controller
{
    public function index()
    {
        $view = new View('@Organisations/index');
        $view->dataTableOrganisations = $this->renderReport('getOrganisation');
        return $this->renderReport('getOrganisation');
    }
}

