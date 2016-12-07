/**
 *  Piwik - free/libre analytics platform

 *  Piwik is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 *  Piwik is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.

 *  @link http://piwik.pro
 *  @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
(function () {
    angular.module('piwikApp').controller('OrganisationsController', OrganisationsController);

    OrganisationsController.$inject = [
      '$scope', '$filter',
      'coreAPI', 'piwik', 'piwikApi',
      'organisationsAdminOrganisationsModel'
    ];

    function OrganisationsController(
        $scope, $filter,
        coreAPI, piwik, piwikApi,
        adminOrganisations
    ) {
        var translate = $filter('translate');

        var init = function () {
            $scope.cacheBuster               = piwik.cacheBuster;
            $scope.hasSuperUserAccess        = piwik.hasSuperUserAccess;
            $scope.organisationIsBeingEdited = false;

            $scope.adminOrganisations              = adminOrganisations;
            $scope.addOrganisation                 = addOrganisation;
            $scope.cancelEditOrganisation          = cancelEditOrganisation;
            $scope.informOrganisationIsBeingEdited = informOrganisationIsBeingEdited;
            $scope.lookupCurrentEditOrganisation   = lookupCurrentEditOrganisation;

            showLoading();
            initOrganisations();
        };

        var initOrganisations = function() {
            adminOrganisations.fetchAvailableOrganisations(function() {
                hideLoading();
            });
        };


        var showLoading = function() {
            $scope.loading = true;
        };

        var hideLoading = function() {
            $scope.loading = false;
        };


        var addOrganisation = function() {
            $scope.adminOrganisations.organisations.unshift({});
        };

        var cancelEditOrganisation = function($event) {
            $event.stopPropagation();

            piwik.helper.redirect($scope.redirectParams);
        };

        var informOrganisationIsBeingEdited = function() {
            $scope.organisationIsBeingEdited = true;
        };

        var lookupCurrentEditOrganisation = function() {
            var organisations           = $scope.adminOrganisations.organisations;
            var organisationsInEditMode = organisations.filter(function(organisation) {
                return organisation.editMode;
            });

            return organisationsInEditMode[0];
        };

        init();
    }
})();