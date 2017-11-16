import CustomerCampaignController from './CustomerCampaignController';
import CustomerCampaignService from './CustomerCampaignService';

const MODULE_NAME = 'client.campaign';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('customer.panel.campaign-list', {
                url: "/campaign",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-campaign-extend-top.html',
                        controller: 'CustomerCampaignController',
                        controllerAs: 'CustomerCampaignCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-campaign.html'),
                        controller: 'CustomerCampaignController',
                        controllerAs: 'CustomerCampaignCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-campaign-extend-bottom.html',
                        controller: 'CustomerCampaignController',
                        controllerAs: 'CustomerCampaignCtrl'
                    }
                },
            })
            .state('customer.panel.bought-campaign-list', {
                url: "/bought",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-campaign-bought-extend-top.html',
                        controller: 'CustomerCampaignController',
                        controllerAs: 'CustomerCampaignCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-campaign-bought.html'),
                        controller: 'CustomerCampaignController',
                        controllerAs: 'CustomerCampaignCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-campaign-bought-extend-bottom.html',
                        controller: 'CustomerCampaignController',
                        controllerAs: 'CustomerCampaignCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/customer-campaign-extend-top.html', '');
        $templateCache.put('templates/customer-campaign-extend-bottom.html', '');

        $templateCache.put('templates/customer-campaign-bought-extend-top.html', '');
        $templateCache.put('templates/customer-campaign-bought-extend-bottom.html', '');

        $http.get(`templates/customer-campaign-bought.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-campaign-bought.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/customer-campaign.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-campaign.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('CustomerCampaignController', CustomerCampaignController)
    .service('CustomerCampaignService', CustomerCampaignService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch (err) {
    throw `${MODULE_NAME} will not be registered`
}
