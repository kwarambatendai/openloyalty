export default class RootController {
    constructor($rootScope, AuthService, $state, $timeout, $translate, $sce, $stateParams, $interval) {
        let self = this;
        this.$state = $state;
        this.$stateParams = $stateParams;
        this.$rootScope = $rootScope;
        this.$timeout = $timeout;
        this.$interval = $interval;
        this.AuthService = AuthService;

        this.translate = $translate;
        this.initialRequests = true;
        this.loadingParts = {
            $$translationsLoaded: false,
            $$contentCompiled: false,
            $$viewLoaded: false,
            allXhrsDone: false,
        };
        this.stateClasses = this._stateClasses();
        let logo = '<svg version="1.1" id="openLoyaltyLogo" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"' +
            '	 viewBox="0 0 200 70" style="enable-background:new 0 0 200 70;" xml:space="preserve">' +
            '<style type="text/css">' +
            '	.st0{fill:#FFFFFF;}' +
            '	.st1{opacity:0.7;}' +
            '</style>' +
            '<g>' +
            '	<path class="st0" d="M109.2,27.4c3.9,0,7,3.2,7,7c0,3.9-3.2,7-7,7c-3.9,0-7-3.2-7-7S105.3,27.4,109.2,27.4 M109.2,26.4' +
            '		c-4.5,0-8.1,3.6-8.1,8.1s3.6,8.1,8.1,8.1s8.1-3.6,8.1-8.1C117.3,30,113.6,26.4,109.2,26.4"/>' +
            '	<path class="st0" d="M55.4,31.2c0,1.7-0.6,3-1.7,3.9C52.6,36,51,36.4,49,36.4h-1.7v6h-2.6v-16h4.6c2,0,3.5,0.4,4.5,1.2' +
            '		C54.9,28.4,55.4,29.6,55.4,31.2 M47.4,34.2h1.4c1.4,0,2.3-0.2,3-0.7c0.6-0.5,0.9-1.2,0.9-2.2c0-0.9-0.3-1.6-0.8-2.1' +
            '		c-0.6-0.5-1.4-0.7-2.6-0.7h-1.8v5.7C47.5,34.2,47.4,34.2,47.4,34.2z"/>' +
            '	<polygon class="st0" points="67.8,42.5 58.7,42.5 58.7,26.4 67.8,26.4 67.8,28.6 61.3,28.6 61.3,33 67.4,33 67.4,35.2 61.3,35.2 ' +
            '		61.3,40.2 67.8,40.2 	"/>' +
            '	<path class="st0" d="M85.4,42.5h-3.2l-7.9-12.9h-0.1l0.1,0.7c0.1,1.4,0.2,2.6,0.2,3.8v8.4h-2.4V26.4h3.2l7.9,12.8h0.1' +
            '		c0-0.2,0-0.8-0.1-1.8c0-1.1-0.1-1.9-0.1-2.5v-8.5h2.4L85.4,42.5L85.4,42.5z"/>' +
            '	<polygon class="st0" points="92,42.5 92,26.4 93.1,26.4 93.1,41.4 100.8,41.4 100.8,42.5 	"/>' +
            '	<polygon class="st0" points="124.5,35.2 129.2,26.4 130.5,26.4 125.1,36.3 125.1,42.5 123.9,42.5 123.9,36.4 118.5,26.4 ' +
            '		119.8,26.4 	"/>' +
            '	<path class="st0" d="M140.5,36.8H134l-2.3,5.7h-1.2l6.5-16.2h0.7l6.4,16.2h-1.3L140.5,36.8z M134.4,35.8h5.8L138,30' +
            '		c-0.2-0.5-0.4-1.1-0.7-1.9c-0.2,0.7-0.4,1.3-0.7,1.9L134.4,35.8z"/>' +
            '	<polygon class="st0" points="147.6,42.5 147.6,26.4 148.8,26.4 148.8,41.4 156.5,41.4 156.5,42.5 	"/>' +
            '	<polygon class="st0" points="162.1,42.5 161,42.5 161,27.4 155.7,27.4 155.7,26.4 167.3,26.4 167.3,27.4 162.1,27.4 	"/>' +
            '	<polygon class="st0" points="174.8,35.2 179.5,26.4 180.7,26.4 175.3,36.3 175.3,42.5 174.2,42.5 174.2,36.4 168.8,26.4 ' +
            '		170.1,26.4 	"/>' +
            '	<g class="st1">' +
            '		<circle class="st0" cx="30.3" cy="33" r="1.7"/>' +
            '	</g>' +
            '	<g class="st1">' +
            '		<path class="st0" d="M22.6,42.2l1.3-2.2c-1.3-1.5-2.1-3.5-2.1-5.6c0-4.7,3.9-8.6,8.6-8.6s8.6,3.9,8.6,8.6c0,2.2-0.8,4.1-2.1,5.6' +
            '			l1.3,2.2c2-2,3.3-4.8,3.3-7.8c0-6.1-4.9-11-11-11s-11,4.9-11,11C19.3,37.4,20.5,40.2,22.6,42.2z"/>' +
            '	</g>' +
            '	<g class="st1">' +
            '		<polygon class="st0" points="35.6,46.6 30.8,38.2 29.8,38.2 25,46.6 22.9,45.4 28.4,35.8 32.2,35.8 37.7,45.4 		"/>' +
            '	</g>' +
            '</g>' +
            '</svg>';

        this.logo = $sce.trustAsHtml(logo);

        this.$rootScope.$on('$includeContentLoaded', () => {
            $(document).foundation();
        });

        this.$rootScope.$on('$translateLoadingEnd', () => {
            this.loadingParts.$$viewLoaded = true;
        });

        this.$rootScope.$on('$stateChangeSuccess', () => {
            let excludedStates = [
                'customer.panel.registration',
                'customer.panel.registration_confirm',
                'customer.panel.registration_from_invitation',
                'customer.panel.registration_success'
            ];

            if (self.$state.includes('admin') || self.$state.includes('customer') || self.$state.includes('seller')) {
                if (!_.includes(excludedStates, self.$state.current.name)) {
                    self.AuthService.setLogoutFrom(self.$state.current.name);
                    self.AuthService.setLogoutFromParams(self.$stateParams);
                }
            }
        });

        this.$rootScope.$on('$stateChangeError', () => {
            //this.loadingParts.stateChanged = true;
        });

        this.$rootScope.$on('$viewContentLoaded', () => {
            $(document).foundation();
            this.loadingParts.$$translationsLoaded = true;
        });

        this.$rootScope.$watch('pendingRequests', () => {
            if ($rootScope.pendingRequests > 0 && !this.loaderInstance && this.initialRequests) {
                self.setLoading(true);
                this.loaderInstance = $interval(() => {
                    if ($rootScope.pendingRequests <= 0) {
                        this.loadingParts.allXhrsDone = true;
                        $rootScope.pendingRequests = 0;
                    }
                    if (this._allTrue(this.loadingParts)) {
                        this._destroyLoader();
                    }

                }, 3500);
            }


        });

        this.contentLoadedTest = $interval(() => {
            let test = angular.element('#contentLoadedTest').text();

            if (test === 'ok') {
                this.loadingParts.$$contentCompiled = true;
                this._destoryContentLoadedTest();
            }

        }, 1000);
    }

    _allTrue(obj) {
        for (var o in obj) {
            if (!obj[o]) return false;
        }

        return true;
    }

    _destroyLoader() {
        if (angular.isDefined(this.loaderInstance)) {
            this.$interval.cancel(this.loaderInstance);
            this.loaderInstance = undefined;
        }

        this.loadingParts.allXhrsDone = false;
        //this.loadingParts.stateChanged = false;

        this.setLoading(false);
        if (this.$state.includes('admin')) {
            this.initialRequests = false;
        }
    }

    _destoryContentLoadedTest() {
        if (angular.isDefined(this.contentLoadedTest)) {
            this.$interval.cancel(this.contentLoadedTest);
            this.contentLoadedTest = undefined;
        }
    }

    _stateClasses() {
        let states = this.$state.get();
        let stateClasses = [];

        for (let i in states) {
            stateClasses[states[i].name] = states[i].name.replace(/\./g, '_');
        }

        return stateClasses;
    }

    foundation() {
        $(document).foundation();
    }

    logout() {
        this.AuthService.logout();
    }


    loggedIn() {
        return this.$state.current.name !== 'admin-login' &&
            this.$state.current.name !== 'customer-login' &&
            this.$state.current.name !== 'seller-login' &&
            this.$state.current.name !== 'customer.panel.registration_success' &&
            this.$state.current.name !== 'customer.panel.registration' &&
            this.$state.current.name !== 'customer.panel.registration_from_invitation';
    }

    setLoading(loading) {
        if (!loading) {
            this.$timeout(() => {
                angular.element('section.loader').fadeOut(500, () => {
                    this.loading = loading;
                })
            }, 1100) //should be greater than default box loader delay
        } else {
            this.loading = loading;
        }
    }

    getViewClass() {
        return this.stateClasses[this.$state.current.name];
    }

    getLogo() {
        return this.logo;
    }

    isAdminPanel() {
        let self = this;

        let adminCustomStates = [
            'admin-login',
            'forgot-password-request-admin',
            'forgot-password-reset-admin'
        ];

        return (self.$state.includes('admin') && !_.includes(adminCustomStates, self.$state.current.name))
    }

    isCustomerPanel() {
        let self = this;

        let customerCustomStates = [
            'customer-login',
            'forgot-password-request-customer',
            'forgot-password-reset-customer',
            'customer.panel.registration_confirm',
            'customer.panel.registration_success',
            'customer.panel.registration',
            'customer.panel.registration_from_invitation'
        ];

        return (self.$state.includes('customer') && !_.includes(customerCustomStates, self.$state.current.name))
    }

    isSellerPanel() {
        let self = this;

        let sellerCustomStates = [
            'seller-login',
            'forgot-password-request-seller',
            'forgot-password-reset-seller'
        ];

        return (self.$state.includes('seller') && !_.includes(sellerCustomStates, self.$state.current.name))

    }

    isClientPanel() {
        return this.isCustomerPanel();
    }

    isPosPanel() {
        return this.isSellerPanel();
    }
}

RootController.$inject = ['$rootScope', 'AuthService', '$state', '$timeout', '$translate', '$sce', '$stateParams', '$interval'];