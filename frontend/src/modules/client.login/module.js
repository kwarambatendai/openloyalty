import CustomerLoginController from './CustomerLoginController';

const MODULE_NAME = 'client.login';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('customer-login', {
                url: "/",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-login-extend-top.html',
                        controller: 'CustomerLoginController',
                        controllerAs: 'CustomerLoginCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-login.html'),
                        controller: 'CustomerLoginController',
                        controllerAs: 'CustomerLoginCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-login-extend-bottom.html',
                        controller: 'CustomerLoginController',
                        controllerAs: 'CustomerLoginCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/customer-login-extend-top.html', '');
        $templateCache.put('templates/customer-login-extend-bottom.html', '');

        $http.get(`templates/customer-login.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-login.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('CustomerLoginController', CustomerLoginController);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch (err) {
    throw `${MODULE_NAME} will not be registered`
}
