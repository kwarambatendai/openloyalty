import TransactionController from './TransactionController';
import TransactionService from './TransactionService';

const MODULE_NAME = 'admin.transactions';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.transaction-list', {
                url: "/admin/transaction-list",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/transactions-list-extend-top.html',
                        controller: 'TransactionController',
                        controllerAs: 'TransactionCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/transactions-list.html'),
                        controller: 'TransactionController',
                        controllerAs: 'TransactionCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/transactions-list-extend-bottom.html',
                        controller: 'TransactionController',
                        controllerAs: 'TransactionCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/transactions-list-extend-top.html', '');
        $templateCache.put('templates/transactions-list-extend-bottom.html', '');

        $http.get(`templates/transactions-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/transactions-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('TransactionController', TransactionController)
    .service('TransactionService', TransactionService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
