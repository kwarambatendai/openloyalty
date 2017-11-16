import CustomerPointsTransferController from './CustomerPointsTransferController';
import CustomerPointsTransferService from './CustomerPointsTransferService';
import CustomerStatusService from './CustomerStatusService';

const MODULE_NAME = 'client.transfers';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('customer.panel.transfers-list', {
                url: "/points/transfer",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-transfers-list-extend-top.html',
                        controller: 'CustomerPointsTransferController',
                        controllerAs: 'CustomerPointsTransferCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-transfers-list.html'),
                        controller: 'CustomerPointsTransferController',
                        controllerAs: 'CustomerPointsTransferCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-transfers-list-extend-bottom.html',
                        controller: 'CustomerPointsTransferController',
                        controllerAs: 'CustomerPointsTransferCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/customer-transfers-list-extend-top.html', '');
        $templateCache.put('templates/customer-transfers-list-extend-bottom.html', '');

        $http.get(`templates/customer-transfers-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-transfers-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('CustomerPointsTransferController', CustomerPointsTransferController)
    .service('CustomerPointsTransferService', CustomerPointsTransferService)
    .service('CustomerStatusService', CustomerStatusService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
