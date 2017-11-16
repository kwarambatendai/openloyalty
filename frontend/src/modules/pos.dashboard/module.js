import SellerDashboardController from './SellerDashboardController';

const MODULE_NAME = 'pos.dashboard';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('seller.panel.dashboard', {
                url: "/dashboard",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-dashboard-extend-top.html',
                        controller: 'SellerDashboardController',
                        controllerAs: 'SellerDashboardCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-dashboard.html'),
                        controller: 'SellerDashboardController',
                        controllerAs: 'SellerDashboardCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-dashboard-extend-bottom.html',
                        controller: 'SellerDashboardController',
                        controllerAs: 'SellerDashboardCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/seller-dashboard-extend-top.html', '');
        $templateCache.put('templates/seller-dashboard-extend-bottom.html', '');

        $http.get(`templates/seller-dashboard.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-dashboard.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('SellerDashboardController', SellerDashboardController)

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
