import Avatar from './../../img/avatar.jpg';
import CustomerController from './CustomerController';
import CustomerService from './CustomerService';

const MODULE_NAME = 'admin.customers';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.customers-list', {
                url: "/customers-list",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customers-list-extend-top.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customers-list.html'),
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customers-list-extend-bottom.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    }
                },
            })
            .state('admin.referred-customers-list', {
                url: "/referred-customers-list",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/referred-customers-list-extend-top.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/referred-customers-list.html'),
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/referred-customers-list-extend-bottom.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    }
                },
            })
            .state('admin.add-customer', {
                url: "/add-customer",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/add-customer-extend-top.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/add-customer.html'),
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/add-customer-extend-bottom.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    }
                }
            })
            .state('admin.edit-customer', {
                url: "/edit-customer/:customerId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/edit-customer-extend-top.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/edit-customer.html'),
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/edit-customer-extend-bottom.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    }
                }
            })
            .state('admin.single-customer', {
                url: "/single-customer/:customerId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/single-customer-extend-top.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/single-customer.html'),
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/single-customer-extend-bottom.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    }
                }
            })
            .state('admin.single-customer.rewards', {
                url: "/rewards",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-rewards-extend-top.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-rewards.html'),
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-rewards-extend-bottom.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    }
                }
            })
            .state('admin.single-customer.transactions', {
                url: "/transactions",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-transactions-extend-top.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-transactions.html'),
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-transactions-extend-bottom.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    }
                }
            })
            .state('admin.single-customer.transfers', {
                url: "/transfers",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-transfers-extend-top.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-transfers.html'),
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-transfers-extend-bottom.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    }
                }
            })
            .state('admin.single-customer.campaigns', {
                url: "/campaigns",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/customer-campaigns-extend-top.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/customer-campaigns.html'),
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/customer-campaigns-extend-bottom.html',
                        controller: 'CustomerController',
                        controllerAs: 'CustomerCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/customers-list-extend-top.html', '');
        $templateCache.put('templates/customers-list-extend-bottom.html', '');

        $templateCache.put('templates/referred-customers-list-extend-top.html', '');
        $templateCache.put('templates/referred-customers-list-extend-bottom.html', '');

        $templateCache.put('templates/add-customer-extend-top.html', '');
        $templateCache.put('templates/add-customer-extend-bottom.html', '');

        $templateCache.put('templates/customer-campaigns-extend-top.html', '');
        $templateCache.put('templates/customer-campaigns-extend-bottom.html', '');

        $templateCache.put('templates/customer-rewards-extend-top.html', '');
        $templateCache.put('templates/customer-rewards-extend-bottom.html', '');

        $templateCache.put('templates/customer-transactions-extend-top.html', '');
        $templateCache.put('templates/customer-transactions-extend-bottom.html', '');

        $templateCache.put('templates/customer-transfers-extend-top.html', '');
        $templateCache.put('templates/customer-transfers-extend-bottom.html', '');

        $templateCache.put('templates/edit-customer-extend-top.html', '');
        $templateCache.put('templates/edit-customer-extend-bottom.html', '');

        $templateCache.put('templates/single-customer-extend-top.html', '');
        $templateCache.put('templates/single-customer-extend-bottom.html', '');

        $templateCache.put('templates/single-customer.modals.html', require('./templates/single-customer.modals.html'));
        $templateCache.put('templates/customers-list.modals.html', require('./templates/customers-list.modals.html'));

        $http.get(`templates/add-customer.html`)
            .then(
                response => {
                    $templateCache.put('templates/add-customer.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/customer-campaigns.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-campaigns.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/customer-rewards.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-rewards.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/customer-transactions.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-transactions.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/customer-transfers.html`)
            .then(
                response => {
                    $templateCache.put('templates/customer-transfers.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/edit-customer.html`)
            .then(
                response => {
                    $templateCache.put('templates/edit-customer.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/single-customer.html`)
            .then(
                response => {
                    $templateCache.put('templates/single-customer.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })

    .controller('CustomerController', CustomerController)
    .service('CustomerService', CustomerService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch (err) {
    throw `${MODULE_NAME} will not be registered`
}
