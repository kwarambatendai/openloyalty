import TransferController from './TransferController';
import TransferService from './TransferService';

const MODULE_NAME = 'admin.transfers';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.transfers-list', {
                url: "/transfers-list",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/transfers-list-extend-top.html',
                        controller: 'TransferController',
                        controllerAs: 'TransferCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/transfers-list.html'),
                        controller: 'TransferController',
                        controllerAs: 'TransferCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/transfers-list-extend-bottom.html',
                        controller: 'TransferController',
                        controllerAs: 'TransferCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/transfers-list-extend-top.html', '');
        $templateCache.put('templates/transfers-list-extend-bottom.html', '');

        $http.get(`templates/transfers-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/transfers-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('TransferController', TransferController)
    .service('TransferService', TransferService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
