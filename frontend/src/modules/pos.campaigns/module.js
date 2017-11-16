import CampaignController from './CampaignController';
import CampaignService from './CampaignService';

const MODULE_NAME = 'pos.campaigns';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('seller.panel.campaigns', {
                url: "/campaigns",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-campaign-list-extend-top.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-campaign-list.html'),
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-campaign-list-extend-bottom.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    }
                },
            })
            .state('seller.single-campaign', {
                url: "/single-campaign/:campaignId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-single-campaign-extend-top.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-single-campaign.html'),
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-single-campaign-extend-bottom.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/seller-single-campaign-extend-top.html', '');
        $templateCache.put('templates/seller-single-campaign-extend-bottom.html', '');

        $templateCache.put('templates/seller-campaign-list-extend-top.html', '');
        $templateCache.put('templates/seller-campaign-list-extend-bottom.html', '');

        $http.get(`templates/seller-campaign-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-campaign-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/seller-single-campaign.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-single-campaign.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('CampaignController', CampaignController)
    .service('CampaignService', CampaignService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
