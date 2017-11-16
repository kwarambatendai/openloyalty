import EarningRuleController from './EarningRuleController';
import EarningRuleService from './EarningRuleService';

const MODULE_NAME = 'admin.earning-rules';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.earning-rule-list', {
                url: "/earning-rules-list",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/earning-rules-list-extend-top.html',
                        controller: 'EarningRuleController',
                        controllerAs: 'EarningRuleCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/earning-rules-list.html'),
                        controller: 'EarningRuleController',
                        controllerAs: 'EarningRuleCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/earning-rules-list-extend-bottom.html',
                        controller: 'EarningRuleController',
                        controllerAs: 'EarningRuleCtrl'
                    }
                }
            })
            .state('admin.add-earning-rule', {
                url: "/admin/add-earning-rule",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/add-earning-rule-extend-top.html',
                        controller: 'EarningRuleController',
                        controllerAs: 'EarningRuleCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/add-earning-rule.html'),
                        controller: 'EarningRuleController',
                        controllerAs: 'EarningRuleCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/add-earning-rule-extend-bottom.html',
                        controller: 'EarningRuleController',
                        controllerAs: 'EarningRuleCtrl'
                    }
                }
            })
            .state('admin.edit-earning-rule', {
                url: "/edit-earning-rule/:earningRuleId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/edit-earning-rule-extend-top.html',
                        controller: 'EarningRuleController',
                        controllerAs: 'EarningRuleCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/edit-earning-rule.html'),
                        controller: 'EarningRuleController',
                        controllerAs: 'EarningRuleCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/edit-earning-rule-extend-bottom.html',
                        controller: 'EarningRuleController',
                        controllerAs: 'EarningRuleCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/edit-earning-rule-extend-top.html', '');
        $templateCache.put('templates/edit-earning-rule-extend-bottom.html', '');

        $templateCache.put('templates/add-earning-rule-extend-top.html', '');
        $templateCache.put('templates/add-earning-rule-extend-bottom.html', '');

        $templateCache.put('templates/earning-rules-list-extend-top.html', '');
        $templateCache.put('templates/earning-rules-list-extend-bottom.html', '');

        $http.get(`templates/earning-rules-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/earning-rules-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/add-earning-rule.html`)
            .then(
                response => {
                    $templateCache.put('templates/add-earning-rule.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/edit-earning-rule.html`)
            .then(
                response => {
                    $templateCache.put('templates/edit-earning-rule.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('EarningRuleController', EarningRuleController)
    .service('EarningRuleService', EarningRuleService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
