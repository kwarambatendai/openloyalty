import LevelController from './LevelController';
import LevelService from './LevelService';

const MODULE_NAME = 'admin.levels';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.levels-list', {
                url: "/levels-list",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/levels-list-extend-top.html',
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/levels-list.html'),
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/levels-list-extend-bottom.html',
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    }
                }
            })
            .state('admin.level-users', {
                url: "/level/:levelId/users/:levelName",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/level-users-extend-top.html',
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/level-users.html'),
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/level-users-extend-bottom.html',
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    }
                }
            })
            .state('admin.add-level', {
                url: "/add-level",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/add-level-extend-top.html',
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/add-level.html'),
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/add-level-extend-bottom.html',
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    }
                }
            })
            .state('admin.edit-level', {
                url: "/edit-level/:levelId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/edit-level-extend-top.html',
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/edit-level.html'),
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/edit-level-extend-bottom.html',
                        controller: 'LevelController',
                        controllerAs: 'LevelCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/edit-level-extend-bottom.html', '');
        $templateCache.put('templates/edit-level-extend-top.html', '');

        $templateCache.put('templates/add-level-extend-bottom.html', '');
        $templateCache.put('templates/add-level-extend-top.html', '');

        $templateCache.put('templates/levels-list-extend-bottom.html', '');
        $templateCache.put('templates/levels-list-extend-top.html', '');

        $templateCache.put('templates/level-users-extend-bottom.html', '');
        $templateCache.put('templates/level-users-extend-top.html', '');

        $http.get(`templates/level-users.html`)
            .then(
                response => {
                    $templateCache.put('templates/level-users.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/levels-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/levels-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/add-level.html`)
            .then(
                response => {
                    $templateCache.put('templates/add-level.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/edit-level.html`)
            .then(
                response => {
                    $templateCache.put('templates/edit-level.tml', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('LevelController', LevelController)
    .service('LevelService', LevelService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch (err) {
    throw `${MODULE_NAME} will not be registered`
}
