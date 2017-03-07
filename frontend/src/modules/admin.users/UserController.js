export default class UserController {
    constructor($scope, $state, $stateParams, AuthService, UserService, Flash, EditableMap, NgTableParams, ParamsMap, $q, LevelService, Validation, $filter, DataService, PosService, TransferService) {
        if (!AuthService.isGranted('ROLE_ADMIN')) {
            $state.go('admin-login')
        }

        this.$scope = $scope;
        this.$scope.newUser = {};
        this.UserService = UserService;
        this.Flash = Flash;
        this.EditableMap = EditableMap;
        this.NgTableParams = NgTableParams;
        this.ParamsMap = ParamsMap;
        this.$state = $state;
        this.$q = $q;
        this.LevelService = LevelService;
        this.Validation = Validation;
        this.$filter = $filter;
        this.config = DataService.getConfig();
        this.userId = $stateParams.userId || null;
        this.loggedUserId = AuthService.getLoggedUserId();
        this.$scope.showMoreFields = this.loggedUserId != this.userId;
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

        this.loaderStates = {
            userList: true,
            userDetails: true,
            coverLoader: true
        }
    }

    getUserData() {
        let self = this;
        self.loaderStates.userDetails = true;

        if (self.userId) {
            self.UserService.getUser(self.userId)
                .then(
                    res => {
                        self.$scope.user = res;
                        self.$scope.editableFields = self.EditableMap.humanizeUser(res);
                        self.loaderStates.userDetails = false;
                        self.loaderStates.coverLoader = false;
                    },
                    () => {
                        let message = self.$filter('translate')('xhr.get_user.error');
                        self.Flash.create('danger', message);
                        self.loaderStates.userDetails = false;
                        self.loaderStates.coverLoader = false;
                    }
                );
        } else {
            self.$state.go('admin.users-list');
            let message = self.$filter('translate')('xhr.get_user.no_id');
            self.Flash.create('warning', message);
            self.loaderStates.userDetails = false;
            self.loaderStates.coverLoader = false;
        }
    }

    getData() {
        let self = this;
        self.loaderStates.userList = true;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage,
            sorting: {
                lastName: 'asc'
            }
        }, {
            getData: function (params) {
                let dfd = self.$q.defer();

                self.UserService.getUsers(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.users = res;
                            params.total(res.total);
                            self.loaderStates.userList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.resolve(res)
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_users.error');
                            self.Flash.create('danger', message);
                            self.loaderStates.userList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

    edit(editedUser) {
        let self = this;
        let validateFields = angular.copy(self.$scope.frontValidate);

        let frontValidation = self.Validation.frontValidation(editedUser, validateFields);
        if (_.isEmpty(frontValidation)) {
            self.UserService.putUser(editedUser, self.loggedUserId != self.userId)
                .then(
                    res => {
                        let message = self.$filter('translate')('xhr.put_user.success');
                        self.Flash.create('success', message);
                        self.$state.go('admin.users-list');
                    },
                    res => {
                        self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                        let message = self.$filter('translate')('xhr.put_user.error');
                        self.Flash.create('danger', message);
                    }
                )
        } else {
            let message = self.$filter('translate')('xhr.put_user.error');
            self.Flash.create('danger', message);
            self.$scope.validate = frontValidation;
        }
    }

    create(newUser) {
        let self = this;
        console.log(newUser);
        let validateFields = angular.copy(self.$scope.frontValidate);

        let frontValidation = self.Validation.frontValidation(newUser, validateFields);
        if (_.isEmpty(frontValidation)) {
            self.UserService.postUser(newUser)
                .then(
                    res => {
                        let message = self.$filter('translate')('xhr.post_user.success');
                        self.Flash.create('success', message);
                        self.$state.go('admin.users-list');
                    },
                    res => {
                        self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                        let message = self.$filter('translate')('xhr.post_user.error');
                        self.Flash.create('danger', message);
                    }
                )
        } else {
            let message = self.$filter('translate')('xhr.post_user.error');
            self.Flash.create('danger', message);
            self.$scope.validate = frontValidation;
        }
    }
}

UserController.$inject = ['$scope', '$state', '$stateParams', 'AuthService', 'UserService', 'Flash', 'EditableMap', 'NgTableParams', 'ParamsMap', '$q', 'LevelService', 'Validation', '$filter', 'DataService', 'PosService', 'TransferService'];