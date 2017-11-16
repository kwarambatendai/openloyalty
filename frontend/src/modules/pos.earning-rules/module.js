import SellerEarningRulesController from './SellerEarningRulesController';
import SellerEarningRulesService from './SellerEarningRulesService';

const MODULE_NAME = 'pos.earning-rules';

angular.module(MODULE_NAME, [])
    .config($stateProvider => {
        $stateProvider
            .state('seller.panel.earning-rules', {
                url: "/earning-rules",
                views: {
                    'extendTop@': {
                        templateUrl: 'templates/seller-earning-rules-list-extend-top.html',
                        controller: 'SellerEarningRulesController',
                        controllerAs: 'SellerEarningRulesCtrl'
                    },
                    'main@': {
                        templateUrl: require('./templates/seller-earning-rules-list.html'),
                        controller: 'SellerEarningRulesController',
                        controllerAs: 'SellerEarningRulesCtrl'
                    },
                    'extendBottom@': {
                        templateUrl: 'templates/seller-earning-rules-list-extend-bottom.html',
                        controller: 'SellerEarningRulesController',
                        controllerAs: 'SellerEarningRulesCtrl'
                    }
                },
            })
    })
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $templateCache.put('templates/seller-earning-rules-list-extend-top.html', '');
        $templateCache.put('templates/seller-earning-rules-list-extend-bottom.html', '');

        $http.get(`templates/seller-earning-rules-list.html`)
            .then(
                response => {
                    $templateCache.put('templates/seller-earning-rules-list.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })
    .controller('SellerEarningRulesController', SellerEarningRulesController)
    .service('SellerEarningRulesService', SellerEarningRulesService);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
