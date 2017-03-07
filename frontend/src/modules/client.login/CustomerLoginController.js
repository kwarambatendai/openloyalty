export default class CustomerLoginController {
    constructor($scope, $state, AuthService) {
        if (AuthService.getStoredRefreshToken()) {
            AuthService.getRefreshToken()
                .then(
                    function (res) {
                        AuthService.setStoredRefreshToken(res.refresh_token);
                        AuthService.setStoredToken(res.token);
                        if (AuthService.isGranted('ROLE_PARTICIPANT')) {
                            $state.go('customer.panel.transactions-list')
                        } else {
                            $state.go('customer-login')
                        }
                    },
                    function () {
                        $state.go('customer-login');
                    }
                )
        }
        this.$scope = $scope;
        this.$state = $state;
        this.AuthService = AuthService;
    }

    submit() {
        var self = this;

        self.AuthService.setLogin(self.$scope._username);
        self.AuthService.setPassword(self.$scope._password);
        self.AuthService.getToken()
            .then(
                function (res) { //success
                    let redirectTo = self.AuthService.getLogoutFrom();
                    let redirectToParams = self.AuthService.getLogoutFromParams();
                    self.AuthService.setStoredToken(res.token);

                    if (self.$scope.rememberMe) {
                        self.AuthService.setStoredRefreshToken(res.refresh_token);
                    }

                    if (redirectTo) {
                        self.$state.go(redirectTo, redirectToParams);
                    } else {
                        self.$state.go('customer.panel.dashboard');
                    }
                },
                function (res) { //error
                    self.$scope.showError = true;
                    self.$scope.errorMsg = res.data.message;
                }
            )
    }
}

CustomerLoginController.$inject = ['$scope', '$state', 'AuthService'];