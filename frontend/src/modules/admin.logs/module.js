import LogsController from './LogsController';
import LogsService from './LogsService';

const MODULE_NAME = 'admin.logs';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.logs', {
                url: "/logs",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/list-logs-extend-top.html',
                        controller: 'LogsController',
                        controllerAs: 'LogsCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/list-logs.html'),
                        controller: 'LogsController',
                        controllerAs: 'LogsCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/list-logs-extend-bottom.html',
                        controller: 'LogsController',
                        controllerAs: 'LogsCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/list-logs-extend-top.html', '');
        $templateCache.put('templates/list-logs-extend-bottom.html', '');

        $http.get(`templates/list-logs.html`)
            .then(
                response => {
                    $templateCache.put('templates/list-logs.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('LogsController', LogsController)
    .service('LogsService', LogsService);
try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
