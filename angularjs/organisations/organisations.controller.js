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