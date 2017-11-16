import Avatar from './../../img/avatar.jpg';
import CustomerProfileController from './CustomerProfileController';
import CustomerProfileService from './CustomerProfileService';

const MODULE_NAME = 'client.profile';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider

            .state('customer.panel.profile-show', {
                url: "/profile",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-profile-extend-top.html',
                        controller: 'CustomerProfileController',
                        controllerAs: 'CustomerProfileCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-profile.html'),
                        controller: 'CustomerProfileController',
                        controllerAs: 'CustomerProfileCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-profile-extend-bottom.html',
                        controller: 'CustomerProfileController',
                        controllerAs: 'CustomerProfileCtrl'
                    }
                },
            })
            .state('customer.panel.profile-edit', {
                url: "/profile/edit",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-profile-edit-extend-top.html',
                        controller: 'CustomerProfileController',
                        controllerAs: 'CustomerProfileCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-profile-edit.html'),
                        controller: 'CustomerProfileController',
                        controllerAs: 'CustomerProfileCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-profile-edit-extend-bottom.html',
                        controller: 'CustomerProfileController',
                        controllerAs: 'CustomerProfileCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/customer-profile-extend-top.html', '');
        $templateCache.put('templates/customer-profile-extend-bottom.html', '');

        $templateCache.put('templates/customer-profile-edit-extend-top.html', '');
        $templateCache.put('templates/customer-profile-edit-extend-bottom.html', '');

        $http.get(`templates/customer-profile.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-profile.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/customer-profile-edit.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-profile-edit.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('CustomerProfileController', CustomerProfileController)
    .service('CustomerProfileService', CustomerProfileService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
