import TranslationsController from './TranslationsController';
import TranslationsService from './TranslationsService';

const MODULE_NAME = 'admin.translations';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.translations', {
                url: "/translations",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/list-translations-extend-top.html',
                        controller: 'TranslationsController',
                        controllerAs: 'TranslationsCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/list-translations.html'),
                        controller: 'TranslationsController',
                        controllerAs: 'TranslationsCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/list-translations-extend-bottom.html',
                        controller: 'TranslationsController',
                        controllerAs: 'TranslationsCtrl'
                    }
                },
            })
            .state('admin.translations_edit', {
                url: "/translations/:translationId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/edit-translations-extend-top.html',
                        controller: 'TranslationsController',
                        controllerAs: 'TranslationsCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/edit-translations.html'),
                        controller: 'TranslationsController',
                        controllerAs: 'TranslationsCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/edit-translations-extend-bottom.html',
                        controller: 'TranslationsController',
                        controllerAs: 'TranslationsCtrl'
                    }
                },
            })
            .state('admin.translations_add', {
                url: "/translations-add",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/add-translations-extend-top.html',
                        controller: 'TranslationsController',
                        controllerAs: 'TranslationsCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/add-translations.html'),
                        controller: 'TranslationsController',
                        controllerAs: 'TranslationsCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/add-translations-extend-bottom.html',
                        controller: 'TranslationsController',
                        controllerAs: 'TranslationsCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/list-translations-extend-top.html', '');
        $templateCache.put('templates/list-translations-extend-bottom.html', '');

        $templateCache.put('templates/edit-translations-extend-top.html', '');
        $templateCache.put('templates/edit-translations-extend-bottom.html', '');

        $templateCache.put('templates/add-translations-extend-top.html', '');
        $templateCache.put('templates/add-translations-extend-bottom.html', '');

        $http.get(`templates/add-translations.html`)
            .then(
                response => {
                    $templateCache.put('templates/add-translations.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/edit-translations.html`)
            .then(
                response => {
                    $templateCache.put('templates/edit-translations.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/list-translations.html`)
            .then(
                response => {
                    $templateCache.put('templates/list-translations.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('TranslationsController', TranslationsController)
    .service('TranslationsService', TranslationsService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
