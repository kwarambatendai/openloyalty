import SegmentController from './SegmentController';
import SegmentService from './SegmentService';
import SegmentCriterionDirective from './SegmentCriterionDirective';
import SegmentPartDirective from './SegmentPartDirective';

const MODULE_NAME = 'admin.segment';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.segment-list', {
                url: "/segment-list/",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/segments-list-extend-top.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/segments-list.html'),
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/segments-list-extend-bottom.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    }
                }
            })
            .state('admin.edit-segment', {
                url: "/edit-segment/:segmentId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/edit-segment-extend-top.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/edit-segment.html'),
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/edit-segment-extend-bottom.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    }
                }
            })
            .state('admin.add-segment', {
                url: "/add-segment",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/add-segment-extend-top.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/add-segment.html'),
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/add-segment-extend-bottom.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    }
                }
            })
            .state('admin.segment-users', {
                url: "/segment-users/:segmentId/:segmentName",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/segment-users-extend-top.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/segment-users.html'),
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/segment-users-extend-bottom.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/segments-list-extend-top.html', '');
        $templateCache.put('templates/segments-list-extend-bottom.html', '');

        $templateCache.put('templates/edit-segment-extend-top.html', '');
        $templateCache.put('templates/edit-segment-extend-bottom.html', '');

        $templateCache.put('templates/add-segment-extend-top.html', '');
        $templateCache.put('templates/add-segment-extend-bottom.html', '');

        $templateCache.put('templates/segment-users-extend-top.html', '');
        $templateCache.put('templates/segment-users-extend-bottom.html', '');

        $http.get(`templates/edit-segment.html`)
            .then(
                response => {
                    $templateCache.put('templates/edit-segment.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/segments-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/segments-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/add-segment.html`)
            .then(
                response => {
                    $templateCache.put('templates/add-segment.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/segment-users.html`)
            .then(
                response => {
                    $templateCache.put('templates/segment-users.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $templateCache.put('templates/anniversary.html', require('./templates/partials/anniversary.html'));
        $templateCache.put('templates/average_transaction_amount.html', require('./templates/partials/average_transaction_amount.html'));
        $templateCache.put('templates/bought_in_pos.html', require('./templates/partials/bought_in_pos.html'));
        $templateCache.put('templates/bought_labels.html', require('./templates/partials/bought_labels.html'));
        $templateCache.put('templates/bought_makers.html', require('./templates/partials/bought_makers.html'));
        $templateCache.put('templates/bought_skus.html', require('./templates/partials/bought_skus.html'));
        $templateCache.put('templates/last_purchase_n_days_before.html', require('./templates/partials/last_purchase_n_days_before.html'));
        $templateCache.put('templates/purchase_period.html', require('./templates/partials/purchase_period.html'));
        $templateCache.put('templates/segment-part.html', require('./templates/partials/segment-part.html'));
        $templateCache.put('templates/transaction_amount.html', require('./templates/partials/transaction_amount.html'));
        $templateCache.put('templates/transaction_count.html', require('./templates/partials/transaction_count.html'));
        $templateCache.put('templates/transaction_percent_in_pos.html', require('./templates/partials/transaction_percent_in_pos.html'));

    })
    .controller('SegmentController', SegmentController)
    .service('SegmentService', SegmentService)
    .directive('segmentPart', () => new SegmentPartDirective())
    .directive('segmentCriterion', () => new SegmentCriterionDirective());

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
