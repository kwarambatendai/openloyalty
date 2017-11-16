import UserController from './UserController';
import UserService from './UserService';

const MODULE_NAME = 'admin.users';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.users-list', {
                url: "/users-list",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/users-list-extend-top.html',
                        controller: 'UserController',
                        controllerAs: 'UserCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/users-list.html'),
                        controller: 'UserController',
                        controllerAs: 'UserCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/users-list-extend-bottom.html',
                        controller: 'UserController',
                        controllerAs: 'UserCtrl'
                    }
                },
            })
            .state('admin.edit-user', {
                url: "/edit-user/:userId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/edit-user-extend-top.html',
                        controller: 'UserController',
                        controllerAs: 'UserCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/edit-user.html'),
                        controller: 'UserController',
                        controllerAs: 'UserCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/edit-user-extend-bottom.html',
                        controller: 'UserController',
                        controllerAs: 'UserCtrl'
                    }
                },
            })
            .state('admin.add-user', {
                url: "/add-user",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/create-user-extend-top.html',
                        controller: 'UserController',
                        controllerAs: 'UserCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/create-user.html'),
                        controller: 'UserController',
                        controllerAs: 'UserCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/create-user-extend-bottom.html',
                        controller: 'UserController',
                        controllerAs: 'UserCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/create-user-extend-top.html', '');
        $templateCache.put('templates/create-user-extend-bottom.html', '');

        $templateCache.put('templates/edit-user-extend-top.html', '');
        $templateCache.put('templates/edit-user-extend-bottom.html', '');

        $templateCache.put('templates/users-list-extend-top.html', '');
        $templateCache.put('templates/users-list-extend-bottom.html', '');

        $http.get(`templates/create-user.html`)
            .then(
                response => {
                    $templateCache.put('templates/create-user.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/edit-user.html`)
            .then(
                response => {
                    $templateCache.put('templates/edit-user.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/users-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/users-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('UserController', UserController)
    .service('UserService', UserService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
