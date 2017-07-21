import CampaignController from './CampaignController';
import CampaignService from './CampaignService';

const MODULE_NAME = 'admin.campaign';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.campaign-list', {
                url: "/campaign-list/",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/campaign-list-extend-top.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/campaign-list.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/campaign-list-extend-bottom.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    }
                }
            })
            .state('admin.edit-campaign', {
                url: "/edit-campaign/:campaignId",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/edit-campaign-extend-top.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/edit-campaign.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/edit-campaign-extend-bottom.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    }
                }
            })
            .state('admin.campaign-customers', {
                url: "/campaign-customers/:campaignId/:campaignName",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/campaign-customers-extend-top.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/campaign-customers.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/campaign-customers-extend-bottom.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    }
                }
            })
            .state('admin.single-campaign', {
                url: "/single-campaign/:campaignId",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/single-campaign-extend-top.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/single-campaign.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/single-campaign-extend-bottom.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    }
                }
            })
            .state('admin.add-campaign', {
                url: "/add-campaign",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/add-campaign-extend-top.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/add-campaign.html',
                        controller: 'CampaignController',
                        controllerAs: 'CampaignCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/add-campaign-extend-bottom.html',
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

        $templateCache.put('./templates/campaign-list-extend-top.html', '');
        $templateCache.put('./templates/campaign-list-extend-bottom.html', '');

        $templateCache.put('./templates/edit-campaign-extend-top.html', '');
        $templateCache.put('./templates/edit-campaign-extend-bottom.html', '');

        $templateCache.put('./templates/campaign-customers-extend-top.html', '');
        $templateCache.put('./templates/campaign-customers-extend-bottom.html', '');

        $templateCache.put('./templates/add-campaign-extend-top.html', '');
        $templateCache.put('./templates/add-campaign-extend-bottom.html', '');

        $templateCache.put('./templates/single-campaign-extend-top.html', '');
        $templateCache.put('./templates/single-campaign-extend-bottom.html', '');

        $http.get(`./build/${MODULE_NAME}/templates/add-campaign.html`)
            .then(
                response => {
                    $templateCache.put('./templates/add-campaign.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/campaign-customers.html`)
            .then(
                response => {
                    $templateCache.put('./templates/campaign-customers.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/campaign-list.html`)
            .then(
                response => {
                    $templateCache.put('./templates/campaign-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/single-campaign.html`)
            .then(
                response => {
                    $templateCache.put('./templates/single-campaign.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('CampaignController', CampaignController)
    .service('CampaignService', CampaignService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch (err) {
    throw `${MODULE_NAME} will not be registered`
}