export default class CustomerDashboardController {
    constructor($scope, $state, AuthService, CustomerStatusService) {
        if (!AuthService.isGranted('ROLE_PARTICIPANT')) {
            $state.go('customer-login')
        }
        this.id = AuthService.getLoggedUserId();

        this.CustomerStatusService = CustomerStatusService;

        this.$scope = $scope;
        this.$state = $state;
        this.AuthService = AuthService;
    }

    getStatus() {
        if (this.id) {
            let self = this;

            this.CustomerStatusService.getStatus(this.id).then(
                res => {
                    self.$scope.status = res;
                    self.$scope.translateValues = {
                      "levelName": res.levelName,
                      "level": res.level,
                      "points": res.points,
                      "pointsToNextLevel": res.pointsToNextLevel,
                      "transactionsAmountToNextLevelWithoutDeliveryCosts": res.transactionsAmountToNextLevelWithoutDeliveryCosts+res.currency,
                      "transactionsAmountToNextLevel": res.transactionsAmountToNextLevel+res.currency,
                    };
                }
            )
        }
    }

}

CustomerDashboardController.$inject = ['$scope', '$state', 'AuthService', 'CustomerStatusService'];