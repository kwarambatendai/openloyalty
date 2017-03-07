var _ = require('lodash');

import EditableMap from './component/global/map/EditableMap';
import ParamsMap from './component/global/map/ParamsMap';

import CheckboxDirective from './component/global/checkbox/CheckboxDirective';
import ModalDirective from './component/global/modal/ModalDirective';
import DatepickerDirective from './component/global/datepicker/DatepickerDirective';
import FormValidationDirective from './component/global/validation/FormValidationDirective';
import CsvUploadDirective from './component/global/csv/CsvUploadDirective';
import SecurityController from './component/global/security/SecurityController';
import SecurityService from './component/global/security/SecurityService';
import DataService from './component/global/data/DataService';
import Validation from './component/global/validation/Validation';
import TranslationService from './component/global/translations/TranslationService';
import TranslationLoader from './component/global/translations/TranslationLoader';
import AuthService from './component/global/auth/AuthService';
import RootController from './component/global/root/RootController';
import StaticPagesController from './component/global/pages/StaticPagesController';
import StaticPagesDirective from './component/global/pages/StaticPagesDirective';

import Filters from './component/global/filters/Filters';

import DebugController from './component/global/debug/DebugController';

if (!window.OpenLoyaltyConfig.debug) {
    if (!window.console) window.console = {};
    var methods = ["log", "debug", "warn", "info"];
    for (var i = 0; i < methods.length; i++) {
        console[methods[i]] = function () {
        };
    }
}

angular.module('OpenLoyalty', [
    'ui.router',
    'angular-jwt',
    'restangular',
    'ngFlash',
    'angularMoment',
    'ngTable',
    'ui.select',
    'ngSanitize',
    'ng-sortable',
    'pascalprecht.translate',
    'selectize',
    'client.campaign',
    'client.dashboard',
    'client.earning-rules',
    'client.login',
    'client.partials',
    'client.profile',
    'client.registration',
    'client.transactions',
    'client.transfers',
])

    .config(function ($stateProvider, $urlRouterProvider, $httpProvider, jwtInterceptorProvider, RestangularProvider, $translateProvider, $locationProvider) {
        let config = window.OpenLoyaltyConfig;

        $locationProvider.hashPrefix('!');
        $translateProvider.useLoader('TranslationLoader');
        $translateProvider.preferredLanguage('en');

        RestangularProvider.setBaseUrl(config.apiUrl);

        RestangularProvider.addResponseInterceptor(function (data, operation, what, url, response, $state) {
            var extractedData = data;

            if (extractedData.customers) {
                for (let i in data.customers) {
                    if (extractedData.customers[i].customerId) {
                        extractedData.customers[i].id = extractedData.customers[i].customerId
                    }
                }
            }
            if (extractedData.customerId) {
                extractedData.id = extractedData.customerId
            }
            if (extractedData.campaignId) {
                extractedData.id = extractedData.campaignId
            }

            if (operation === "getList") {
                let keys = Object.keys(data);
                extractedData = data[keys[0]];
                extractedData.total = data.total;
            }

            return extractedData;
        });

        jwtInterceptorProvider.tokenGetter = ['AuthService', 'jwtHelper', 'config', '$state', function (AuthService, jwtHelper, config, $state) {
            if (config.url.indexOf('login_check') !== -1 || config.url.indexOf('token/refresh') !== -1) {
                return null;
            }

            let idToken = AuthService.getStoredToken();

            if (idToken && jwtHelper.isTokenExpired(idToken) && AuthService.isRememberMe()) {
                return AuthService.getRefreshToken()
                    .then(
                        res => {
                            let id_token = res.token;
                            let refresh_token = res.refresh_token;

                            AuthService.setStoredRefreshToken(refresh_token);
                            AuthService.setStoredToken(id_token);

                            return id_token;
                        },
                        res => {
                            AuthService.logout();
                        }
                    );
            } else if (idToken && !jwtHelper.isTokenExpired(idToken)) {
                return idToken;
            } else {
                return null
            }
        }];

        $httpProvider.interceptors.push('jwtInterceptor');

        $urlRouterProvider
            .otherwise("/");
        $stateProvider
            .state('customer', {
                url: "/customer"
            })
            .state('customer.panel', {
                url: "/panel",
                resolve: {
                    DataServiceResolver: ['DataService', function (DataService) {
                        return DataService.getAvailableData()
                    }]
                }
            })
            .state('customer.static', {
                url: "/page/:pageName",
                views: {
                    '@': {
                        templateUrl: './templates/static-pages.html',
                        controller: 'StaticPagesController',
                        controllerAs: 'StaticPagesCtrl'
                    }
                }
            })


        if (window.OpenLoyaltyConfig.debug) {
            $stateProvider.state('debug', {
                url: "/debug",
                views: {
                    '@': {
                        templateUrl: './templates/debug.html',
                        controller: 'DebugController',
                        controllerAs: 'DebugCtrl'
                    }
                }
            });
        }
    })

    .run(['Restangular', '$state', 'AuthService', '$rootScope', '$templateCache', function (Restangular, $state, AuthService, $rootScope, $templateCache) {
        $rootScope.pendingRequests = _.isNumber($rootScope.pendingRequests) ? $rootScope.pendingRequests : 0;
        Restangular.setErrorInterceptor(function (response) {
            $rootScope.pendingRequests -= 1;
            if (response.data.message && response.data.message === 'Bad credentials') {
                return true;
            }
            if (response.status === 401) {
                AuthService.logout();
                return false;
            }
            return true;
        });
        Restangular.addResponseInterceptor(res => {
            $rootScope.pendingRequests -= 1;

            return res;
        });
        Restangular.addRequestInterceptor(req => {
            $rootScope.pendingRequests += 1;

            return req;
        });
        $templateCache.put('ng-table/filters/text.html', '<input type="text" name="{{name}}" ng-disabled="$filterRow.disabled" ng-model="params.filter()[name]" class="input-filter form-control" placeholder="{{getFilterPlaceholderValue(filter, name)}}" ng-model-options="{debounce: 1500}" /> ');
        $templateCache.put('ng-table/filters/number.html', '<input type="number" name="{{name}}" ng-disabled="$filterRow.disabled" ng-model="params.filter()[name]" class="input-filter form-control" placeholder="{{getFilterPlaceholderValue(filter, name)}}" ng-model-options="{debounce: 1500}" /> ');

    }])

    .filter('commaToDot', () => new Filters.CommaToDecimal())
    .filter('percent', () => new Filters.Percent())
    .filter('propsFilter', () => new Filters.PropsFilter())
    .filter('isEmpty', () => new Filters.IsEmptyFilter())

    .directive('modal', () => new ModalDirective())
    .directive('datepicker', () => new DatepickerDirective())
    .directive('formValidation', () => new FormValidationDirective())
    .directive('csvUpload', () => new CsvUploadDirective())
    .directive('checkbox', () => new CheckboxDirective())
    .directive('staticPage', () => new StaticPagesDirective())

    .service('Validation', Validation)
    .service('EditableMap', EditableMap)
    .service('ParamsMap', ParamsMap)
    .service('DataService', DataService)
    .service('Validation', Validation)
    .service('AuthService', AuthService)
    .service('SecurityService', SecurityService)
    .service('TranslationService', TranslationService)
    .factory('TranslationLoader', ['TranslationService', (TranslationService) => new TranslationLoader(TranslationService)])

    .controller('RootController', RootController)
    .controller('SecurityController', SecurityController)
    .controller('StaticPagesController', StaticPagesController)
    .controller('DebugController', DebugController)