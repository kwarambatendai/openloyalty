import SellerTransactionController from './SellerTransactionController';
import SellerTransactionService from './SellerTransactionService';

const MODULE_NAME = 'pos.transactions';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('seller.panel.transaction', {
                url: "/transaction",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-transactions-extend-top.html',
                        controller: 'SellerTransactionController',
                        controllerAs: 'SellerTransactionCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-transactions.html'),
                        controller: 'SellerTransactionController',
                        controllerAs: 'SellerTransactionCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-transactions-extend-bottom.html',
                        controller: 'SellerTransactionController',
                        controllerAs: 'SellerTransactionCtrl'
                    }
                },
            })
            .state('seller.panel.find-transaction', {
                url: "/transaction/find",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-find-transaction-extend-top.html',
                        controller: 'SellerTransactionController',
                        controllerAs: 'SellerTransactionCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-find-transaction.html'),
                        controller: 'SellerTransactionController',
                        controllerAs: 'SellerTransactionCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-find-transaction-extend-bottom.html',
                        controller: 'SellerTransactionController',
                        controllerAs: 'SellerTransactionCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/seller-transactions-extend-top.html', '');
        $templateCache.put('templates/seller-transactions-extend-bottom.html', '');

        $templateCache.put('templates/seller-find-transaction-extend-top.html', '');
        $templateCache.put('templates/seller-find-transaction-extend-bottom.html', '');

        $http.get(`templates/seller-find-transaction.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-find-transaction.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/seller-transactions.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-transactions.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('SellerTransactionController', SellerTransactionController)
    .service('SellerTransactionService', SellerTransactionService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
