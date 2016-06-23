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
