import CustomerDashboardController from './CustomerDashboardController';

const MODULE_NAME = 'client.dashboard';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('customer.panel.dashboard', {
                url: "/dashboard",
                views: {
                    'extendTop@': {
                        templateUrl: './templates/customer-dashboard-extend-top.html',
                        controller: 'CustomerDashboardController',
                        controllerAs: 'CustomerDashboardCtrl'
                    },
                    'main@': {
                        templateUrl: './templates/customer-dashboard.html',
                        controller: 'CustomerDashboardController',
                        controllerAs: 'CustomerDashboardCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: './templates/customer-dashboard-extend-bottom.html',
                        controller: 'CustomerDashboardController',
                        controllerAs: 'CustomerDashboardCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('./templates/customer-dashboard-extend-top.html', '');
        $templateCache.put('./templates/customer-dashboard-extend-bottom.html', '');

        $http.get(`./build/${MODULE_NAME}/templates/customer-dashboard.html`)
            .then(
                response => {
                    $templateCache.put('./templates/customer-dashboard.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('CustomerDashboardController', CustomerDashboardController);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}