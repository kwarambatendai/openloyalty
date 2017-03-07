export default class SellerController {
    constructor($scope, $state, AuthService, SellerService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, DataService, PosService, Validation, $filter) {
        if (!AuthService.isGranted('ROLE_ADMIN')) {
            AuthService.logout();
        }
        this.$scope = $scope;
        this.SellerService = SellerService;
        this.$state = $state;
        this.Flash = Flash;
        this.$scope.newSeller = {};
        this.$scope.editableFields = {};
        this.sellerId = $stateParams.sellerId || null;
        this.NgTableParams = NgTableParams;
        this.ParamsMap = ParamsMap;
        this.EditableMap = EditableMap;
        this.$q = $q;
        this.Validation = Validation;
        this.$filter = $filter;
        this.config = DataService.getConfig();
        this.posConfig = {
            valueField: 'posId',
            labelField: 'name',
            create: false,
            sortField: 'name',
            searchField: 'name',
            maxItems: 1,
        };
        this.active = [
            {
                name: this.$filter('translate')('global.active'),
                type: 1
            },
            {
                name: this.$filter('translate')('global.inactive'),
                type: 0
            }
        ];
        this.activeConfig = {
            valueField: 'type',
            labelField: 'name',
            create: false,
            sortField: 'name',
            maxItems: 1
        };
        this.$scope.frontValidate = {
            firstName: '@assert:not_blank',
            lastName: '@assert:not_blank',
            email: '@assert:not_blank',
            plainPassword: '@assert:not_blank',
            posId: '@assert:not_blank',
        };
        this.posPromise = PosService.getPosList({})
            .then(
                res => {
                    $scope.pos = res;
                    this.pos = res;
                },
                () => {

                }
            );

        this.loaderStates = {
            sellerList: true,
            sellerDetails: true,
            coverLoader: true,
            removeSeller: false
        }
    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage
        }, {
            getData: params => {
                let dfd = self.$q.defer();
                self.loaderStates.sellerList = true;

                self.SellerService.getSellers(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.sellers = res;
                            params.total(res.total);
                            self.loaderStates.sellerList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.resolve(res);
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_sellers.error');
                            self.Flash.create('danger', message);
                            self.loaderStates.sellerList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

    getSellerData() {
        let self = this;
        self.loaderStates.sellerDetails = true;

        self.posPromise
            .then(() => {
                if (self.sellerId) {
                    self.SellerService.getSeller(self.sellerId)
                        .then(
                            res => {
                                self.$scope.seller = res;
                                self.$scope.editableFields = self.EditableMap.humanizeSeller(res, self.$scope.pos);
                                self.loaderStates.sellerDetails = false;
                                self.loaderStates.coverLoader = false;
                            },
                            () => {
                                let message = self.$filter('translate')('xhr.get_seller.error');
                                self.Flash.create('danger', message);
                                self.loaderStates.sellerDetails = false;
                                self.loaderStates.coverLoader = false;
                            }
                        )
                } else {
                    self.$state.go('admin.seller-list');
                    let message = self.$filter('translate')('xhr.get_seller.no_id');
                    self.Flash.create('warning', message);
                    self.loaderStates.sellerDetails = false;
                }
            })
    }

    editSeller(editedSeller) {
        let self = this;
        let validateFields = angular.copy(self.$scope.frontValidate);
        delete validateFields.plainPassword;
        let frontValidation = self.Validation.frontValidation(editedSeller, validateFields);
        if (_.isEmpty(frontValidation)) {
            self.SellerService.putSeller(self.sellerId, editedSeller)
                .then(
                    res => {
                        let message = self.$filter('translate')('xhr.put_seller.success');
                        self.Flash.create('success', message);
                        self.$state.go('admin.seller-list');
                    },
                    res => {
                        self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                        let message = self.$filter('translate')('xhr.put_seller.error');
                        self.Flash.create('danger', message);
                    }
                )
        } else {
            self.$scope.validate = frontValidation;
            let message = self.$filter('translate')('xhr.put_seller.error');
            self.Flash.create('danger', message);
        }
    }

    addSeller(newSeller) {
        let self = this;
        let validateFields = angular.copy(self.$scope.frontValidate);
        let frontValidation = self.Validation.frontValidation(newSeller, validateFields);

        if (_.isEmpty(frontValidation)) {
            self.SellerService.postSeller(newSeller)
                .then(
                    res => {
                        let message = self.$filter('translate')('xhr.post_seller.success');
                        self.Flash.create('success', message);
                        self.$state.go('admin.seller-list');
                    },
                    res => {
                        self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                        let message = self.$filter('translate')('xhr.post_seller.error');
                        self.Flash.create('danger', message);
                    }
                );
        } else {
            self.$scope.validate = frontValidation;
            let message = self.$filter('translate')('xhr.put_seller.error');
            self.Flash.create('danger', message);
        }
    }

    setSellerState(state, sellerId) {
        let self = this;

        if (state) {
            self.SellerService.postActivateSeller(sellerId)
                .then(
                    () => {
                        let message = self.$filter('translate')('xhr.post_activate_seller.success');
                        self.Flash.create('success', message);
                        self.tableParams.reload();
                    },
                    () => {
                        let message = self.$filter('translate')('xhr.post_activate_seller.error');
                        self.Flash.create('danger', message);
                    }
                )
        } else {
            self.SellerService.postDeactivateSeller(sellerId)
                .then(
                    () => {
                        let message = self.$filter('translate')('xhr.post_deactivate_seller.success');
                        self.Flash.create('success', message);
                        self.tableParams.reload();
                    },
                    () => {
                        let message = self.$filter('translate')('xhr.post_deactivate_seller.error');
                        self.Flash.create('danger', message);
                    }
                )
        }
    }

    removeSeller(sellerId) {
        let self = this;
        self.loaderStates.removeSeller = true;

        self.SellerService.postDeleteSeller(sellerId)
            .then(
                () => {
                    let message = self.$filter('translate')('xhr.post_delete_seller.success');
                    self.Flash.create('success', message);
                    self.tableParams.reload();
                    self.loaderStates.removeSeller = false;
                },
                () => {
                    let message = self.$filter('translate')('xhr.post_delete_seller.error');
                    self.Flash.create('danger', message);
                    self.loaderStates.removeSeller = false;
                }
            )
    }
}

SellerController.$inject = ['$scope', '$state', 'AuthService', 'SellerService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', '$stateParams', 'EditableMap', 'DataService', 'PosService', 'Validation', '$filter'];