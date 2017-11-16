import PosController from './PosController';
import PosService from './PosService';

const MODULE_NAME = 'admin.pos';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.pos-list', {
                url: "/pos-list/",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/pos-list-extend-top.html',
                        controller: 'PosController',
                        controllerAs: 'PosCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/pos-list.html'),
                        controller: 'PosController',
                        controllerAs: 'PosCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/pos-list-extend-bottom.html',
                        controller: 'PosController',
                        controllerAs: 'PosCtrl'
                    }
                }
            })
            .state('admin.edit-pos', {
                url: "/edit-pos/:posId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/edit-pos-extend-top.html',
                        controller: 'PosController',
                        controllerAs: 'PosCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/edit-pos.html'),
                        controller: 'PosController',
                        controllerAs: 'PosCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/edit-pos-extend-bottom.html',
                        controller: 'PosController',
                        controllerAs: 'PosCtrl'
                    }
                }
            })
            .state('admin.add-pos', {
                url: "/add-pos",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/add-pos-extend-top.html',
                        controller: 'PosController',
                        controllerAs: 'PosCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/add-pos.html'),
                        controller: 'PosController',
                        controllerAs: 'PosCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/add-pos-extend-bottom.html',
                        controller: 'PosController',
                        controllerAs: 'PosCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/edit-pos-extend-top.html', '');
        $templateCache.put('templates/edit-pos-extend-bottom.html', '');

        $templateCache.put('templates/add-pos-extend-top.html', '');
        $templateCache.put('templates/add-pos-extend-bottom.html', '');

        $templateCache.put('templates/pos-list-extend-top.html', '');
        $templateCache.put('templates/pos-list-extend-bottom.html', '');

        $http.get(`templates/pos-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/pos-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/edit-pos.html`)
            .then(
                response => {
                    $templateCache.put('templates/edit-pos.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/add-pos.html`)
            .then(
                response => {
                    $templateCache.put('templates/add-pos.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('PosController', PosController)
    .service('PosService', PosService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
