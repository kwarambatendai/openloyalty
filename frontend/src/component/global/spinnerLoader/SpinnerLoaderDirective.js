/**
 * Describes Spinner Loader
 *
 * @class SpinnerLoaderDirective
 * @constructor
 */
export default class SpinnerLoaderDirective {
    /**
     * @method constructor
     */
    constructor($timeout) {
        this.restrict = 'E';
        this.scope = {
            loading: '=?',
            delay: '=?'
        };
        this.templateUrl = require('./templates/spinner-loader.html');
        this.link = (scope, element) => {
            element.parent().addClass('loader-relative');

            scope.delay = scope.delay ? scope.delay : 1000;
            scope.cover = scope.cover ? 1 : 0;

            scope.$watch('loading', (newValue) => {
                if (!newValue) {
                    $timeout(() => {
                        element.fadeOut(500, () => {
                            element.parent().addClass('out').removeClass('in')

                        })
                    }, scope.delay)
                } else {
                    element.fadeIn(500, () => {
                        element.parent().addClass('in').removeClass('out')
                    })
                }
            });
        }
    }

    /**
     * Creates Loader
     *
     * @returns {SpinnerLoaderDirective}
     */
    static create() {
        return new SpinnerLoaderDirective(...arguments);
    }
}

SpinnerLoaderDirective.create.$inject = ['$timeout'];
