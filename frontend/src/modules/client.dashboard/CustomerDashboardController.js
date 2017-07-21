export default class CustomerDashboardController {
    constructor($scope, $state, AuthService, CustomerStatusService, Validation, InvitationService, $filter, Flash) {
        if (!AuthService.isGranted('ROLE_PARTICIPANT')) {
            $state.go('customer-login')
        }
        this.id = AuthService.getLoggedUserId();
        this.InvitationService = InvitationService;
        this.CustomerStatusService = CustomerStatusService;
        this.Validation = Validation;
        this.$scope = $scope;
        this.$state = $state;
        this.AuthService = AuthService;
        this.$scope.loader = true;
        this.$scope.frontValidate = {
            recipientEmail: '@assert:not_blank',
        };
        this.$scope.invitation = {};
        this.Flash = Flash;
        this.$filter = $filter;
    }

    inviteUser(invitation) {
        let self = this;
        let validateFields = angular.copy(self.$scope.frontValidate);
        let frontValidation = self.Validation.frontValidation(invitation, validateFields);
        if (_.isEmpty(frontValidation)) {
            self.InvitationService.invite(invitation)
                .then(
                    res => {
                        let message = self.$filter('translate')('xhr.post_invitation.success');
                        self.Flash.create('success', message);
                        self.$state.reload();
                    },
                    res => {
                        self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                        let message = self.$filter('translate')('xhr.post_invitation.error');
                        self.Flash.create('danger', message);
                    }
                )
        } else {
            self.$scope.validate = frontValidation;
        }
    }

    getStatus() {
        if (this.id) {
            let self = this;

            this.CustomerStatusService.getStatus(this.id).then(
                res => {
                    self.$scope.loader = false;
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

CustomerDashboardController.$inject = ['$scope', '$state', 'AuthService', 'CustomerStatusService', 'Validation', 'InvitationService', '$filter', 'Flash'];
