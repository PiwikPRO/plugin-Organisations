/**
 * Piwik PRO - Premium functionality and enterprise-level support for Piwik Analytics
 *
 * @link http://piwik.pro
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
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