export default class SellerDashboardController {
    constructor($scope, $state, AuthService) {
        if (!AuthService.isGranted('ROLE_SELLER')) {
            $state.go('seller-login')
        }
        this.$scope = $scope;
        this.$state = $state;
        this.AuthService = AuthService;
    }

}

SellerDashboardController.$inject = ['$scope', '$state', 'AuthService'];