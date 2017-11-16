import Avatar from './../../img/avatar.jpg';
import SellerCustomerController from './SellerCustomerController';
import SellerCustomerService from './SellerCustomerService';

const MODULE_NAME = 'pos.customers';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('seller.panel.customer-search', {
                url: "/search/customer",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-find-customer-extend-top.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-find-customer.html'),
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-find-customer-extend-bottom.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    }
                },
            })
            .state('seller.panel.customer-registration', {
                url: "/customer-registration",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-customer-registration-extend-top.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-customer-registration.html'),
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-customer-registration-extend-bottom.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    }
                },
            })
            .state('seller.panel.edit-customer', {
                url: "/customer-edit/:customerId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-customer-edit-extend-top.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-customer-edit.html'),
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-customer-edit-extend-bottom.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    }
                },
            })
            .state('seller.panel.single-customer', {
                url: "/single-customer/:customerId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-single-customer-extend-top.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-single-customer.html'),
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-single-customer-extend-bottom.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    }
                }
            })
            .state('seller.panel.single-customer.rewards', {
                url: "/rewards",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-customer-rewards-extend-top.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-customer-rewards.html'),
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-customer-rewards-extend-bottom.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    }
                }
            })
            .state('seller.panel.single-customer.transactions', {
                url: "/transactions",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-customer-transactions-extend-top.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-customer-transactions.html'),
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-customer-transactions-extend-bottom.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    }
                }
            })
            .state('seller.panel.single-customer.transfers', {
                url: "/transfers",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-customer-transfers-extend-top.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-customer-transfers.html'),
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-customer-transfers-extend-bottom.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    }
                }
            })
            .state('seller.panel.single-customer.campaigns', {
                url: "/campaigns",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-customer-campaigns-extend-top.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-customer-campaigns.html'),
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-customer-campaigns-extend-bottom.html',
                        controller: 'SellerCustomerController',
                        controllerAs: 'SellerCustomerCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/seller-find-customer-extend-top.html', '');
        $templateCache.put('templates/seller-find-customer-extend-bottom.html', '');

        $templateCache.put('templates/seller-customer-registration-extend-top.html', '');
        $templateCache.put('templates/seller-customer-registration-extend-bottom.html', '');

        $templateCache.put('templates/seller-customer-edit-extend-top.html', '');
        $templateCache.put('templates/seller-customer-edit-extend-bottom.html', '');

        $templateCache.put('templates/seller-single-customer-extend-top.html', '');
        $templateCache.put('templates/seller-single-customer-extend-bottom.html', '');

        $templateCache.put('templates/seller-customer-rewards-extend-top.html', '');
        $templateCache.put('templates/seller-customer-rewards-extend-bottom.html', '');

        $templateCache.put('templates/seller-customer-transactions-extend-top.html', '');
        $templateCache.put('templates/seller-customer-transactions-extend-bottom.html', '');

        $templateCache.put('templates/seller-customer-transfers-extend-top.html', '');
        $templateCache.put('templates/seller-customer-transfers-extend-bottom.html', '');

        $templateCache.put('templates/seller-customer-campaigns-extend-top.html', '');
        $templateCache.put('templates/seller-customer-campaigns-extend-bottom.html', '');

        $http.get(`templates/seller-find-customer.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-find-customer.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/seller-customer-registration.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-customer-registration.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/seller-customer-edit.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-customer-edit.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/seller-single-customer.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-single-customer.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/seller-customer-rewards.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-customer-rewards.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/seller-customer-transactions.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-customer-transactions.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/seller-customer-transfers.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-customer-transfers.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/seller-customer-campaigns.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-customer-campaigns.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('SellerCustomerController', SellerCustomerController)
    .service('SellerCustomerService', SellerCustomerService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
