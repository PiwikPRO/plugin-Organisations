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
    angular.module('piwikApp').factory('organisationsAdminOrganisationsModel', organisationsAdminOrganisationsModel);

    organisationsAdminOrganisationsModel.$inject = [ 'piwikApi' ];

    function organisationsAdminOrganisationsModel(piwikApi)
    {
        var model = {
            organisations : [],
            isLoading     : false,

            fetchAvailableOrganisations: fetchAvailableOrganisations
        };

        return model;

        function onError()
        {
            setOrganisations([]);
        }

        function setOrganisations(organisations)
        {
            model.organisations = organisations;
        }

        function fetchAvailableOrganisations(callback)
        {
            if (model.isLoading) {
                piwikApi.abort();
            }

            model.isLoading = true;

            var params = {
                method: 'Organisations.getAvailableOrganisations'
            };

            return piwikApi.fetch(params).then(function (organisations) {
                if (!organisations) {
                    onError();
                    return;
                }

                setOrganisations(organisations);
            }, onError)['finally'](function () {
                if (callback) {
                    callback();
                }

                model.isLoading = false;
            });
        }
    }
})();