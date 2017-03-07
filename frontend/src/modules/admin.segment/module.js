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
                        templateUrl: './templates/segments-list-extend-top.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/segments-list.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/segments-list-extend-bottom.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    }
                }
            })
            .state('admin.edit-segment', {
                url: "/edit-segment/:segmentId",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/edit-segment-extend-top.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/edit-segment.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/edit-segment-extend-bottom.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    }
                }
            })
            .state('admin.add-segment', {
                url: "/add-segment",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/add-segment-extend-top.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/add-segment.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/add-segment-extend-bottom.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    }
                }
            })
            .state('admin.segment-users', {
                url: "/segment-users/:segmentId/:segmentName",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/segment-users-extend-top.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/segment-users.html',
                        controller: 'SegmentController',
                        controllerAs: 'SegmentCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/segment-users-extend-bottom.html',
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

        $templateCache.put('./templates/segments-list-extend-top.html', '');
        $templateCache.put('./templates/segments-list-extend-bottom.html', '');

        $templateCache.put('./templates/edit-segment-extend-top.html', '');
        $templateCache.put('./templates/edit-segment-extend-bottom.html', '');

        $templateCache.put('./templates/add-segment-extend-top.html', '');
        $templateCache.put('./templates/add-segment-extend-bottom.html', '');

        $templateCache.put('./templates/segment-users-extend-top.html', '');
        $templateCache.put('./templates/segment-users-extend-bottom.html', '');

        $http.get(`./build/${MODULE_NAME}/templates/edit-segment.html`)
            .then(
                response => {
                    $templateCache.put('./templates/edit-segment.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/segments-list.html`)
            .then(
                response => {
                    $templateCache.put('./templates/segments-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/add-segment.html`)
            .then(
                response => {
                    $templateCache.put('./templates/add-segment.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/segment-users.html`)
            .then(
                response => {
                    $templateCache.put('./templates/segment-users.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/anniversary.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/anniversary.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/average_transaction_amount.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/average_transaction_amount.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/bought_in_pos.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/bought_in_pos.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/bought_labels.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/bought_labels.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/bought_makers.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/bought_makers.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/bought_skus.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/bought_skus.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/last_purchase_n_days_before.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/last_purchase_n_days_before.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/purchase_period.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/purchase_period.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/segment-part.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/segment-part.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/transaction_amount.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/transaction_amount.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/transaction_count.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/transaction_count.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/partials/transaction_percent_in_pos.html`)
            .then(
                response => {
                    $templateCache.put('./templates/partials/transaction_percent_in_pos.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
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