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
    angular.module('piwikApp').directive('organisationsMultilineField', organisationsMultilineField);

    function organisationsMultilineField() {
        return {
            restrict: 'A',
            replace: true,
            scope: {
                managedValue: '=field',
                rows: '@?',
                cols: '@?'
            },
            templateUrl: 'plugins/Organisations/angularjs/organisations/multiline-field.directive.html?cb=' + piwik.cacheBuster,
            link: function(scope) {
                var separator = '\n';

                var init = function() {
                    scope.field    = {};
                    scope.onChange = updateManagedScopeValue;

                    scope.$watch('managedValue', updateInputValue);
                };

                var updateManagedScopeValue = function() {
                    scope.managedValue = scope.field.value.trim().split(separator);
                };

                var updateInputValue = function() {
                    if (angular.isUndefined(scope.managedValue)) {
                        return;
                    }

                    scope.field.value = scope.managedValue.join(separator);
                };

                init();
            }
        };
    }
})();
