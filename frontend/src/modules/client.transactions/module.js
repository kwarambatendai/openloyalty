import CustomerTransactionController from './CustomerTransactionController';
import CustomerTransactionService from './CustomerTransactionService';

const MODULE_NAME = 'client.transactions';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('customer.panel.transactions-list', {
                url: "/transactions",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-transactions-list-extend-top.html',
                        controller: 'CustomerTransactionController',
                        controllerAs: 'CustomerTransactionCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-transactions-list.html'),
                        controller: 'CustomerTransactionController',
                        controllerAs: 'CustomerTransactionCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-transactions-list-extend-bottom.html',
                        controller: 'CustomerTransactionController',
                        controllerAs: 'CustomerTransactionCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/customer-transactions-list-extend-top.html', '');
        $templateCache.put('templates/customer-transactions-list-extend-bottom.html', '');

        $http.get(`templates/customer-transactions-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-transactions-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('CustomerTransactionController', CustomerTransactionController)
    .service('CustomerTransactionService', CustomerTransactionService)

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
