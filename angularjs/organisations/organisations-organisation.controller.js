(function () {
    angular.module('piwikApp').controller('OrganisationsOrganisationController', OrganisationsOrganisationController);

    OrganisationsOrganisationController.$inject = [ '$scope', '$filter' ];

    function OrganisationsOrganisationController($scope, $filter) {
        var translate = $filter('translate');

        var init = function () {
            $scope.openDeleteDialog = openDeleteDialog;

            $scope.editOrganisation = editOrganisation;
            $scope.saveOrganisation = saveOrganisation;

            $scope.organisation.delete       = deleteOrganisation;
            $scope.organisation.deleteDialog = {};
            $scope.organisation.editDialog   = {};

            if (organisationIsNew()) {
                initNewOrganisation();
            }
        };

        var initNewOrganisation = function() {
            $scope.informOrganisationIsBeingEdited();

            $scope.organisation.editMode = true;
            $scope.organisation.name     = '';
            $scope.organisation.ipranges = [];
        };


        var deleteOrganisation = function() {
            var ajaxHandler = new ajaxHelper();

            ajaxHandler.addParams({
                idOrg:  $scope.organisation.idorg,
                module: 'API',
                format: 'json',
                method: 'Organisations.deleteOrganisation'
            }, 'GET');

            ajaxHandler.redirectOnSuccess($scope.redirectParams);
            ajaxHandler.setLoadingElement();
            ajaxHandler.send(true);
        };

        var editOrganisation = function() {
            if ($scope.organisationIsBeingEdited) {
                $scope.organisation.editDialog.show  = true;
                $scope.organisation.editDialog.title = translate(
                    'Organisations_OnlyOneOrganisationAtTime',
                    '"' + $scope.lookupCurrentEditOrganisation().name + '"'
                );

                return;
            }

            $scope.organisation.editMode = true;

            $scope.informOrganisationIsBeingEdited();
        };

        var openDeleteDialog = function() {
            $scope.organisation.deleteDialog.show  = true;
            $scope.organisation.deleteDialog.title = translate(
                'Organisations_DeleteConfirm',
                '"' + $scope.organisation.name + '"'
            );
        };

        var organisationIsNew = function() {
            return angular.isUndefined($scope.organisation.idorg);
        };

        var saveOrganisation = function() {
            var ajaxHandler = new ajaxHelper();

            ajaxHandler.addParams({
                module: 'API',
                format: 'json'
            }, 'GET');

            if (organisationIsNew()) {
                ajaxHandler.addParams({
                    method: 'Organisations.addOrganisation'
                }, 'GET');
            } else {
                ajaxHandler.addParams({
                    idOrg: $scope.organisation.idorg,
                    method: 'Organisations.updateOrganisation'
                }, 'GET');
            }

            ajaxHandler.addParams({
                name:     $scope.organisation.name,
                ipRanges: $scope.organisation.ipranges
            }, 'POST');

            ajaxHandler.redirectOnSuccess($scope.redirectParams);
            ajaxHandler.setLoadingElement();
            ajaxHandler.send(true);
        };

        init();
    }
})();