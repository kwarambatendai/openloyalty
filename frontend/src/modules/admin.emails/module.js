import EmailsController from './EmailsController';
import EmailsService from './EmailsService';

const MODULE_NAME = 'admin.emails';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.emails', {
                url: "/emails",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/list-emails-extend-top.html',
                        controller: 'EmailsController',
                        controllerAs: 'EmailsCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/list-emails.html'),
                        controller: 'EmailsController',
                        controllerAs: 'EmailsCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/list-emails-extend-bottom.html',
                        controller: 'EmailsController',
                        controllerAs: 'EmailsCtrl'
                    }
                },
            })
            .state('admin.emails_edit', {
                url: "/emails/:emailId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/edit-email-extend-top.html',
                        controller: 'EmailsController',
                        controllerAs: 'EmailsCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/edit-email.html'),
                        controller: 'EmailsController',
                        controllerAs: 'EmailsCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/edit-email-extend-bottom.html',
                        controller: 'EmailsController',
                        controllerAs: 'EmailsCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/list-emails-extend-top.html', '');
        $templateCache.put('templates/list-emails-extend-bottom.html', '');

        $templateCache.put('templates/edit-email-extend-top.html', '');
        $templateCache.put('templates/edit-email-extend-bottom.html', '');

        $http.get(`templates/edit-email.html`)
            .then(
                response => {
                    $templateCache.put('templates/edit-email.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/list-emails.html`)
            .then(
                response => {
                    $templateCache.put('templates/list-emails.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('EmailsController', EmailsController)
    .service('EmailsService', EmailsService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
