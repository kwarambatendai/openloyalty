/**
 * Describes Loader
 *
 * @class BoxLoaderDirective
 * @constructor
 */
export default class BoxLoaderDirective {
    /**
     * @method constructor
     */
    constructor($timeout) {
        this.restrict = 'E';
        this.scope = {
            loading: '=?',
            delay: '=?',
            cover: '=?'
        };
        this.templateUrl = require('./templates/box-loader.html');
        this.link = (scope, element) => {
            element.parent().addClass('loader-relative');

            scope.delay = scope.delay ? scope.delay : 0;
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
     * @returns {BoxLoaderDirective}
     */
    static create() {
        return new BoxLoaderDirective(...arguments);
    }
}

BoxLoaderDirective.create.$inject = ['$timeout'];
