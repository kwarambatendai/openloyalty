export default class ModalDirective {
    constructor() {
        this.restrict = 'E';
        this.scope = {
            show: '=',
            id: '=modalId',
            size: '=',
            type: '=',
            after: '=',
            modalTitle: '=',
            minWidth: '=',
            restoreOnExit: '='
        };
        this.replace = true;
        this.transclude = true;
        this.link = function (scope, element) {
            scope.restored = scope.restoreOnExit;

            scope.hideModal = function ($event) {
                if ($event.target.className === 'modal-bg' || $event.target.className === 'close-modal') {
                    scope.show = false;
                    scope.restoreOnExit = scope.restored;
                }
            };

            if (scope.size === 'tiny') {
                element.find('.modal').removeClass('small').addClass('tiny')
            }

            if (scope.size === 'tiny-small') {
                element.find('.modal').removeClass('small').addClass('tiny-small')
            }


            if (scope.minWidth) {
                element.find('.modal').get(0).style.minWidth = scope.minWidth;
            }
        };
        this.templateUrl = require('./templates/modal.html');
    }
}

ModalDirective.$inject = [];
