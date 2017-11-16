import SettingsController from './SettingsController';
import SettingsService from './SettingsService';

const MODULE_NAME = 'admin.settings';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.settings', {
                url: "/settings",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/admin-settings-extend-top.html',
                        controller: 'SettingsController',
                        controllerAs: 'SettingsCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/admin-settings.html'),
                        controller: 'SettingsController',
                        controllerAs: 'SettingsCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/admin-settings-extend-bottom.html',
                        controller: 'SettingsController',
                        controllerAs: 'SettingsCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/admin-settings-extend-top.html', '');
        $templateCache.put('templates/admin-settings-extend-bottom.html', '');

        $http.get(`templates/admin-settings.html`)
            .then(
                response => {
                    $templateCache.put('templates/admin-settings.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })

    .controller('SettingsController', SettingsController)
    .service('SettingsService', SettingsService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
