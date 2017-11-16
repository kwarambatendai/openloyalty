import AdminDataController from './AdminDataController';
import AdminDataService from './AdminDataService';

const MODULE_NAME = 'admin.data';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.data', {
                url: "/admin-data",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/edit-admin-data-extend-top.html',
                        controller: 'AdminDataController',
                        controllerAs: 'AdminDataCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/edit-admin-data.html'),
                        controller: 'AdminDataController',
                        controllerAs: 'AdminDataCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/edit-admin-data-extend-bottom.html',
                        controller: 'AdminDataController',
                        controllerAs: 'AdminDataCtrl'
                    },
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/edit-admin-data-extend-top.html', '');
        $templateCache.put('templates/edit-admin-data-extend-bottom.html', '');

        $http.get(`templates/edit-admin-data.html`)
            .then(
                response => {
                    $templateCache.put('templates/edit-admin-data.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('AdminDataController', AdminDataController)
    .service('AdminDataService', AdminDataService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
