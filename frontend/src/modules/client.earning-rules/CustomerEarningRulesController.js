export default class CustomerEarningRulesController {
    constructor($scope, $state, AuthService, CustomerEarningRulesService, Flash,ParamsMap, $stateParams, EditableMap) {
        if (!AuthService.isGranted('ROLE_PARTICIPANT')) {
            $state.go('customer-login')
        }
        this.$scope = $scope;
        this.CustomerEarningRulesService = CustomerEarningRulesService;
        this.$state = $state;
        this.Flash = Flash;
        this.ParamsMap = ParamsMap;
        this.EditableMap = EditableMap;
    }

    getRules() {
        let self = this;

        this.CustomerEarningRulesService.getRules().then(
            res => {
                self.$scope.rules = res.earningRules;
                self.$scope.currency = res.currency;
            }
        )
    }
}

CustomerEarningRulesController.$inject = ['$scope', '$state', 'AuthService', 'CustomerEarningRulesService', 'Flash', 'ParamsMap', '$stateParams', 'EditableMap'];