export default class CustomerPointsTransferController {
    constructor($scope, $state, AuthService, CustomerPointsTransferService, CustomerStatusService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, $filter, DataService) {
        if (!AuthService.isGranted('ROLE_PARTICIPANT')) {
            $state.go('customer-login')
        }
        this.id = AuthService.getLoggedUserId();

        this.$scope = $scope;
        this.CustomerPointsTransferService = CustomerPointsTransferService;
        this.CustomerStatusService = CustomerStatusService;
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

    getStatus() {
        if (this.id) {
            let self = this;

            this.CustomerStatusService.getStatus(this.id).then(
                res => {
                    self.$scope.status = res;
                }
            )
        }
    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage,
            sorting: {
                createdAt: 'desc'
            }
        }, {
            getData: function (params) {
                let dfd = self.$q.defer();

                self.CustomerPointsTransferService.getTransfers(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.loader = false;
                            self.$scope.transfers = res;
                            params.total(res.total);
                            dfd.resolve(res);
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_transfers.error');
                            self.Flash.create('danger', message);
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

}

CustomerPointsTransferController.$inject = ['$scope', '$state', 'AuthService', 'CustomerPointsTransferService', 'CustomerStatusService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', '$stateParams', 'EditableMap', '$filter', 'DataService'];