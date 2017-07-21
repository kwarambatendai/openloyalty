export default class SellerEarningRulesController {
    constructor($scope, $state, AuthService, SellerEarningRulesService, DataService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, Validation, $filter) {
        if (!AuthService.isGranted('ROLE_SELLER')) {
            AuthService.logout();
        }
        this.$scope = $scope;
        this.SellerEarningRulesService = SellerEarningRulesService;
        this.$state = $state;
        this.Flash = Flash;
        this.earningRuleId = $stateParams.earningRuleId || null;
        this.NgTableParams = NgTableParams;
        this.Validation = Validation;
        this.$filter = $filter;
        this.$q = $q;
        this.ParamsMap = ParamsMap;
        this.loaderVisible = true;
        this.types = {
            "points": $filter('translate')('earning_rule.types.points'),
            "event": $filter('translate')('earning_rule.types.event'),
            "product_purchase": $filter('translate')('earning_rule.types.product_purchase'),
            "multiply_for_product": $filter('translate')('earning_rule.types.multiply_for_product'),
        }
    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({}, {
            getData: function (params) {
                let dfd = self.$q.defer();

                self.SellerEarningRulesService.getEarningRules(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.earningRules = res;
                            self.loaderVisible = false;
                            params.total(res.total);
                            dfd.resolve(res);
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_earning_rules.error');
                            self.Flash.create('danger', message);
                            self.loaderVisible = false;
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

    getEarningRuleData() {
        let self = this;

        if (self.earningRuleId) {
            self.EarningRuleService.getEarningRule(self.earningRuleId)
                .then(
                    res => {
                        self.$scope.earningRule = res;
                        self.$scope.editableFields = self.EditableMap.humanizeEarningRuleFields(res);
                    },
                    () => {
                        let message = self.$filter('translate')('xhr.get_earning_rule.error');
                        self.Flash.create('danger', message);
                    }
                )
        } else {
            self.$state.go('admin.earning-rule-list');
            let message = self.$filter('translate')('xhr.get_earning_rule.no_id');
            self.Flash.create('warning', message);
        }
    }
}

SellerEarningRulesController.$inject = ['$scope', '$state', 'AuthService', 'SellerEarningRulesService', 'DataService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', '$stateParams', 'EditableMap', 'Validation', '$filter'];