import CustomerRegistrationController from './CustomerRegistrationController';
import CustomerRegistrationService from './CustomerRegistrationService';

const MODULE_NAME = 'client.registration';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('customer.panel.registration', {
                url: "/customer/registration",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/customer-registration-extend-top.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/customer-registration.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/customer-registration-extend-bottom.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    }
                }
            })
            .state('customer.panel.registration_from_invitation', {
                url: "/customer/registration/:invitationToken",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/customer-registration-extend-top.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/customer-registration.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/customer-registration-extend-bottom.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    }
                }
            })
            .state('customer.panel.registration_success', {
                url: "/customer/registration-success",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/customer-registration-success-extend-top.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/customer-registration-success.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/customer-registration-success-extend-bottom.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    }
                }
            })
            .state('customer.panel.registration_confirm', {
                url: "/customer/registration/activate/:token",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/customer-registration-activate-extend-top.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/customer-registration-activate.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/customer-registration-activate-extend-bottom.html',
                        controller: 'CustomerRegistrationController',
                        controllerAs: 'CustomerRegistrationCtrl'
                    }
                }
            })
            .state('forgot-password-request-customer', {
                url: "/customer/password/request",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/customer-password-request-extend-top.html',
                        controller: 'SecurityController',
                        controllerAs: 'SecurityCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/customer-password-request.html',
                        controller: 'SecurityController',
                        controllerAs: 'SecurityCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/customer-password-request-extend-bottom.html',
                        controller: 'SecurityController',
                        controllerAs: 'SecurityCtrl'
                    }
                }
            })
            .state('forgot-password-reset-customer', {
                url: "/password/reset/:token",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/customer-password-reset-extend-top.html',
                        controller: 'SecurityController',
                        controllerAs: 'SecurityCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/customer-password-reset.html',
                        controller: 'SecurityController',
                        controllerAs: 'SecurityCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/customer-password-reset-extend-bottom.html',
                        controller: 'SecurityController',
                        controllerAs: 'SecurityCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('./templates/customer-registration-extend-top.html', '');
        $templateCache.put('./templates/customer-registration-extend-bottom.html', '');

        $templateCache.put('./templates/customer-registration-success-extend-top.html', '');
        $templateCache.put('./templates/customer-registration-success-extend-bottom.html', '');

        $templateCache.put('./templates/customer-registration-activate-extend-top.html', '');
        $templateCache.put('./templates/customer-registration-activate-extend-bottom.html', '');

        $templateCache.put('./templates/customer-password-request-extend-top.html', '');
        $templateCache.put('./templates/customer-password-request-extend-bottom.html', '');

        $templateCache.put('./templates/customer-password-reset-extend-top.html', '');
        $templateCache.put('./templates/customer-password-reset-extend-bottom.html', '');

        $http.get(`./build/${MODULE_NAME}/templates/customer-registration.html`)
            .then(
                response => {
                    $templateCache.put('./templates/customer-registration.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/customer-registration-success.html`)
            .then(
                response => {
                    $templateCache.put('./templates/customer-registration-success.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/customer-registration-activate.html`)
            .then(
                response => {
                    $templateCache.put('./templates/customer-registration-activate.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/customer-password-reset.html`)
            .then(
                response => {
                    $templateCache.put('./templates/customer-password-reset.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/customer-password-request.html`)
            .then(
                response => {
                    $templateCache.put('./templates/customer-password-request.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('CustomerRegistrationController', CustomerRegistrationController)
    .service('CustomerRegistrationService', CustomerRegistrationService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}