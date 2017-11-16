import SellerController from './SellerController';
import SellerService from './SellerService';

const MODULE_NAME = 'admin.seller';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('admin.seller-list', {
                url: "/seller-list/",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/sellers-list-extend-top.html',
                        controller: 'SellerController',
                        controllerAs: 'SellerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/sellers-list.html'),
                        controller: 'SellerController',
                        controllerAs: 'SellerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/sellers-list-extend-top.html',
                        controller: 'SellerController',
                        controllerAs: 'SellerCtrl'
                    }
                }
            })
            .state('admin.edit-seller', {
                url: "/edit-seller/:sellerId",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/edit-seller-extend-top.html',
                        controller: 'SellerController',
                        controllerAs: 'SellerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/edit-seller.html'),
                        controller: 'SellerController',
                        controllerAs: 'SellerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/edit-seller-extend-top.html',
                        controller: 'SellerController',
                        controllerAs: 'SellerCtrl'
                    }
                }
            })
            .state('admin.add-seller', {
                url: "/add-seller",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/add-seller-extend-top.html',
                        controller: 'SellerController',
                        controllerAs: 'SellerCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/add-seller.html'),
                        controller: 'SellerController',
                        controllerAs: 'SellerCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/add-seller-extend-top.html',
                        controller: 'SellerController',
                        controllerAs: 'SellerCtrl'
                    }
                }
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/sellers-list-extend-top.html', '');
        $templateCache.put('templates/sellers-list-extend-bottom.html', '');

        $templateCache.put('templates/edit-seller-extend-top.html', '');
        $templateCache.put('templates/edit-seller-extend-bottom.html', '');

        $templateCache.put('templates/add-seller-extend-top.html', '');
        $templateCache.put('templates/add-seller-extend-bottom.html', '');

        $http.get(`templates/sellers-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/sellers-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/edit-seller.html`)
            .then(
                response => {
                    $templateCache.put('templates/edit-seller.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`templates/add-seller.html`)
            .then(
                response => {
                    $templateCache.put('templates/add-seller.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('SellerController', SellerController)
    .service('SellerService', SellerService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
