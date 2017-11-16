export default class CheckboxDirective {
    constructor() {
        this.restrict = 'E';
        this.require = '?ngModel';
        this.templateUrl = require('./templates/checkbox.html');
        this.scope = {
            value: '=ngModel'
        };
        this.link = function (scope, element, attrs, ctrl) {
            ctrl.$modelValue ? element.addClass('checked') : element.removeClass('checked');

            scope.switchCheckbox = () => {
                if(!attrs.disabled) {
                    ctrl.$setViewValue(!ctrl.$modelValue);
                    ctrl.$render();
                }
            };

            scope.$watch('value', () => {
                ctrl.$modelValue ? element.addClass('checked') : element.removeClass('checked');
            })
        };
    }
}

CheckboxDirective.$inject = [];
