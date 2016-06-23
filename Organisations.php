<?php
namespace Piwik\Plugins\Organisations;

use Piwik\ArchiveProcessor;
use Piwik\Db;

class Organisations extends \Piwik\Plugin
{
    /**
     * @see Piwik\Plugin::registerEvents
     */
    public function registerEvents()
    {
        return array(
            'AssetManager.getJavaScriptFiles'        => 'getJsFiles',
            'AssetManager.getStylesheetFiles'        => 'getStylesheetFiles',
            'Live.getAllVisitorDetails'              => 'extendVisitorDetails',
            'Tracker.setTrackerCacheGeneral'         => 'setTrackerCacheGeneral',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys'
        );
    }


    public function setTrackerCacheGeneral(&$cacheContent)
    {
        $model = new Model();
        return $model->setTrackerCache($cacheContent);
    }

    /**
     * Extends visitor details with organisation related information
     *
     * @param array $visitor
     * @param array $details
     */
    public function extendVisitorDetails(&$visitor, $details)
    {
        $instance = new Visitor($details);

        $visitor['organisation']     = $instance->getOrganisation();
        $visitor['organisationName'] = $instance->getOrganisationName();
    }

    public function install()
    {
        Model::install();
    }

    public function getClientSideTranslationKeys(&$translationKeys)
    {
        $translationKeys[] = 'Organisations_AddOrganisation';
        $translationKeys[] = 'Organisations_DeleteConfirm';
        $translationKeys[] = 'Organisations_IpRanges';
        $translationKeys[] = 'Organisations_IpRangesHelp';
        $translationKeys[] = 'Organisations_IpRangesPlaceholder';
        $translationKeys[] = 'Organisations_MainDescription';
        $translationKeys[] = 'Organisations_Name';
        $translationKeys[] = 'Organisations_NamePlaceholder';
        $translationKeys[] = 'Organisations_OnlyOneOrganisationAtTime';
        $translationKeys[] = 'Organisations_Organisations';
        $translationKeys[] = 'Organisations_OrganisationsManagement';
    }

    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = 'plugins/Organisations/angularjs/organisations/multiline-field.directive.js';
        $jsFiles[] = 'plugins/Organisations/angularjs/organisations/organisations.controller.js';
        $jsFiles[] = 'plugins/Organisations/angularjs/organisations/organisations-admin-organisations-model.js';
        $jsFiles[] = 'plugins/Organisations/angularjs/organisations/organisations-organisation.controller.js';
    }

    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = 'plugins/Organisations/stylesheets/Organisations.less';
        $stylesheets[] = 'plugins/Morpheus/stylesheets/base.less';
    }
}
