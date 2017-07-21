export default class CustomerTransactionController {
    constructor($scope, $state, AuthService, CustomerTransactionService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, $filter, DataService) {
        if (!AuthService.isGranted('ROLE_PARTICIPANT')) {
            $state.go('customer-login')
        }
        this.$scope = $scope;
        this.CustomerTransactionService = CustomerTransactionService;
        this.$state = $state;
        this.Flash = Flash;
        this.NgTableParams = NgTableParams;
        this.ParamsMap = ParamsMap;
        this.EditableMap = EditableMap;
        this.$q = $q;
        this.$filter = $filter;
        this.config = DataService.getConfig();
        this.$scope.loader = true;
    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage
        }, {
            getData: function (params) {
                let dfd = self.$q.defer();

                self.CustomerTransactionService.getTransactions(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.loader = false;
                            self.$scope.transactions = res;
                            params.total(res.total);
                            dfd.resolve(res);
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_transations.error');
                            self.Flash.create('danger', message);
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

}

CustomerTransactionController.$inject = ['$scope', '$state', 'AuthService', 'CustomerTransactionService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', '$stateParams', 'EditableMap', '$filter', 'DataService'];