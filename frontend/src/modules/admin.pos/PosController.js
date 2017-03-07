export default class PosController {
    constructor($scope, $state, AuthService, PosService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, DataService, Validation, $filter) {
        if (!AuthService.isGranted('ROLE_ADMIN')) {
            AuthService.logout();
        }
        this.$scope = $scope;
        this.PosService = PosService;
        this.$state = $state;
        this.Flash = Flash;
        this.$scope.newPos = {};
        this.$scope.editableFields = {};
        this.posId = $stateParams.posId || null;
        this.NgTableParams = NgTableParams;
        this.ParamsMap = ParamsMap;
        this.EditableMap = EditableMap;
        this.countries = DataService.getCountries();
        this.$q = $q;
        this.Validation = Validation;
        this.$filter = $filter;
        this.config = DataService.getConfig();
        this.$scope.frontValidate = {
            name: '@assert:not_blank',
            identifier: '@assert:not_blank',
            location: {
                street: '@assert:not_blank',
                address1: '@assert:not_blank',
                postal: '@assert:not_blank',
                city: '@assert:not_blank',
                province: '@assert:not_blank',
                country: '@assert:not_blank',
            }
        };

        this.countryConfig = {
            valueField: 'code',
            labelField: 'name',
            create: false,
            sortField: 'name',
            searchField: 'name',
            maxItems: 1,
        };

        this.loaderStates = {
            posList: true,
            posDetails: true
        }
    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage
        }, {
            getData: function (params) {
                let dfd = self.$q.defer();

                self.loaderStates.posList = true;
                self.PosService.getPosList(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.posList = res;
                            params.total(res.total);
                            self.loaderStates.posList = false;
                            dfd.resolve(res);
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_pos_list.error');
                            self.Flash.create('danger', message);
                            self.loaderStates.posList = false;
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

    getPosData() {
        let self = this;
        self.loaderStates.posDetails = true;

        if (self.posId) {
            self.PosService.getPos(self.posId)
                .then(
                    res => {
                        self.$scope.pos = res;
                        self.$scope.editableFields = self.EditableMap.humanizePos(res);
                        self.loaderStates.posDetails = false;
                    },
                    () => {
                        let message = self.$filter('translate')('xhr.get_pos.error');
                        self.Flash.create('danger', message);
                        self.loaderStates.posDetails = false;
                    }
                )
        } else {
            self.$state.go('admin.pos-list');
            let message = self.$filter('translate')('xhr.get_pos.no_id');
            self.Flash.create('warning', message);
            self.loaderStates.posDetails = false;
        }
    }

    editPos(editedPos) {
        let self = this;
        let validateFields = angular.copy(self.$scope.frontValidate);
        let frontValidation = self.Validation.frontValidation(editedPos, validateFields);
        self.loaderStates.posDetails = true;

        if (_.isEmpty(frontValidation)) {
            delete editedPos.transactionsAmount
            delete editedPos.transactionsCount
            self.PosService.putPos(self.posId, editedPos)
                .then(
                    res => {
                        let message = self.$filter('translate')('xhr.put_pos.success');
                        self.Flash.create('success', message);
                        self.$state.go('admin.pos-list')
                        self.loaderStates.posDetails = false;
                    },
                    res => {
                        self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                        let message = self.$filter('translate')('xhr.put_pos.error');
                        self.Flash.create('danger', message);
                        self.loaderStates.posDetails = false;
                    }
                )
        } else {
            self.$scope.validate = frontValidation;
            let message = self.$filter('translate')('xhr.put_pos.error');
            self.Flash.create('danger', message);
            self.loaderStates.posDetails = false;
        }
    }

    addPos(newPos) {
        let self = this;
        let validateFields = angular.copy(self.$scope.frontValidate);
        let frontValidation = self.Validation.frontValidation(newPos, validateFields);
        self.loaderStates.posDetails = true;

        if (_.isEmpty(frontValidation)) {
            self.PosService.postPos(newPos)
                .then(
                    res => {
                        let message = self.$filter('translate')('xhr.post_pos.success');
                        self.Flash.create('success', message);
                        self.$state.go('admin.pos-list')
                        self.loaderStates.posDetails = false;
                    },
                    res => {
                        self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                        let message = self.$filter('translate')('xhr.post_pos.error');
                        self.Flash.create('danger', message);
                        self.loaderStates.posDetails = false;
                    }
                );
        } else {
            self.$scope.validate = frontValidation;
            let message = self.$filter('translate')('xhr.post_pos.error');
            self.Flash.create('danger', message);
            self.loaderStates.posDetails = false;
        }
    }
}

PosController.$inject = ['$scope', '$state', 'AuthService', 'PosService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', '$stateParams', 'EditableMap', 'DataService', 'Validation', '$filter'];