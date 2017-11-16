import CustomerEarningRulesController from './CustomerEarningRulesController';
import CustomerEarningRulesService from './CustomerEarningRulesService';

const MODULE_NAME = 'client.earning-rules';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('customer.panel.earning_rules', {
                url: "/earningRules",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-earning-rules-extend-top.html',
                        controller: 'CustomerEarningRulesController',
                        controllerAs: 'CustomerEarningRulesCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-earning-rules.html'),
                        controller: 'CustomerEarningRulesController',
                        controllerAs: 'CustomerEarningRulesCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-earning-rules-extend-bottom.html',
                        controller: 'CustomerEarningRulesController',
                        controllerAs: 'CustomerEarningRulesCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/customer-earning-rules-extend-top.html', '');
        $templateCache.put('templates/customer-earning-rules-extend-bottom.html', '');

        $http.get(`templates/customer-earning-rules.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-earning-rules.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('CustomerEarningRulesController', CustomerEarningRulesController)
    .service('CustomerEarningRulesService', CustomerEarningRulesService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
