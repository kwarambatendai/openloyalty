import DashboardController from './DashboardController';

const MODULE_NAME = 'admin.dashboard';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin', {
                url: "/admin",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/dashboard-extend-top.html',
                        controller: 'DashboardController',
                        controllerAs: 'DashboardCtrl',
                        resolve: {
                            DataServiceResolver: ['DataService', function (DataService) {
                                return DataService.getAvailableData()
                            }]
                        }
                    },
                    'main@': {
                        templateUrl: require('./templates/dashboard.html'),
                        controller: 'DashboardController',
                        controllerAs: 'DashboardCtrl',
                        resolve: {
                            DataServiceResolver: ['DataService', function (DataService) {
                                return DataService.getAvailableData()
                            }]
                        }
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/dashboard-extend-bottom.html',
                        controller: 'DashboardController',
                        controllerAs: 'DashboardCtrl',
                        resolve: {
                            DataServiceResolver: ['DataService', function (DataService) {
                                return DataService.getAvailableData()
                            }]
                        }
                    },
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/dashboard-extend-top.html', '');
        $templateCache.put('templates/dashboard-extend-bottom.html', '');

        $http.get(`templates/dashboard.html`)
            .then(
                response => {
                    $templateCache.put('templates/dashboard.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('DashboardController', DashboardController);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch (err) {
    throw `${MODULE_NAME} will not be registered`
}
