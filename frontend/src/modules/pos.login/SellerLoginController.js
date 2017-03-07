export default class SellerLoginController {
    constructor($scope, $state, AuthService) {
        if (AuthService.getStoredRefreshToken()) {
            AuthService.getRefreshToken()
                .then(
                    res => {
                        AuthService.setStoredRefreshToken(res.refresh_token);
                        AuthService.setStoredToken(res.token);
                        if (AuthService.isGranted('ROLE_SELLER')) {
                            $state.go('seller.panel.dashboard')
                        } else {
                            $state.go('seller-login')
                        }
                    },
                    () => {
                        $state.go('seller-login');
                    }
                )
        }
        this.$scope = $scope;
        this.$state = $state;
        this.AuthService = AuthService;
    }

    submit() {
        let self = this;

        self.AuthService.setLogin(self.$scope._username);
        self.AuthService.setPassword(self.$scope._password);
        self.AuthService.getToken()
            .then(
                res => { //success
                    let redirectTo = self.AuthService.getLogoutFrom();

                    self.AuthService.setStoredToken(res.token);

                    if (self.$scope.rememberMe) {
                        self.AuthService.setStoredRefreshToken(res.refresh_token);
                    }

                    if (redirectTo) {
                        self.$state.go(redirectTo);
                    } else {
                        self.$state.go('seller.panel.dashboard');
                    }
                },
                res => { //error
                    self.$scope.showError = true;
                    self.$scope.errorMsg = res.data.message;
                }
            )
    }
}

SellerLoginController.$inject = ['$scope', '$state', 'AuthService'];