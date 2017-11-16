export default class AdminDataController {
    constructor($scope, $state, $stateParams, AuthService, AdminDataService, Flash, EditableMap, NgTableParams, ParamsMap, $q, LevelService, Validation, $filter, DataService, PosService) {
        if (!AuthService.isGranted('ROLE_ADMIN')) {
            $state.go('admin-login')
        }
        this.$scope = $scope;
        this.$scope.dateNow = new Date();
        this.AdminDataService = AdminDataService;
        this.Flash = Flash;
        this.EditableMap = EditableMap;
        this.$q = $q;
        this.Validation = Validation;
        this.$filter = $filter;
        this.$scope.password = {};
        this.$scope.passwordValidate = {
            currentPassword: '@assert:not_blank',
            plainPassword: '@assert:not_blank|equal_with:plainPassword2',
            plainPassword2: '@assert:not_blank|equal_with:plainPassword'
        };
        this.$scope.frontValidate = {
            email: '@assert:not_blank'
        };
        this.loaderStates = {
            adminData: true,
            coverLoader: true
        }
    }

    changePassword(password) {
        let self = this;
        let validateFields = angular.copy(self.$scope.passwordValidate);
        let frontValidation = self.Validation.frontValidation(password, validateFields);

        if (_.isEmpty(frontValidation)) {
            self.AdminDataService.changePassword(password)
                .then(
                    res => {
                        let message = self.$filter('translate')('xhr.post_password_change.success');
                        self.Flash.create('success', message);
                    },
                    res => {
                        self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                        let message = self.$filter('translate')('xhr.post_password_change.error');
                        self.Flash.create('danger', message);
                    }
                )
        } else {
            let message = self.$filter('translate')('xhr.post_password_change.error');
            self.Flash.create('danger', message);
            self.$scope.validate = frontValidation;
        }
    }

    getData() {
        let self = this;
        self.loaderStates.adminData = true;

        self.AdminDataService.getAdminData()
            .then(
                res => {
                    self.$scope.editableFields = res;
                    self.loaderStates.adminData = false;
                    self.loaderStates.coverLoader = false;
                },
                () => {
                    let message = self.$filter('translate')('xhr.get_admin_data.error');
                    self.Flash.create('danger', message);
                    self.loaderStates.adminData = false;
                    self.loaderStates.coverLoader = false;
                }
            );
    }


    editAdminData(edited) {
        let self = this;
        let validateFields = angular.copy(self.$scope.frontValidate);
        let frontValidation = self.Validation.frontValidation(edited, validateFields);

        if (_.isEmpty(frontValidation)) {
            self.AdminDataService.putAdminData(edited)
                .then(
                    res => {
                        let message = self.$filter('translate')('xhr.put_admin_data.success');
                        self.Flash.create('success', message);
                        self.$scope.validate = {};
                    },
                    res => {
                        self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                        let message = self.$filter('translate')('xhr.put_admin_data.error');
                        self.Flash.create('danger', message);
                    }
                )
        } else {
            let message = self.$filter('translate')('xhr.put_admin_data.error');
            self.Flash.create('danger', message);
            self.$scope.validate = frontValidation;
        }
    }

}

AdminDataController.$inject = ['$scope', '$state', '$stateParams', 'AuthService', 'AdminDataService', 'Flash', 'EditableMap', 'NgTableParams', 'ParamsMap', '$q', 'LevelService', 'Validation', '$filter', 'DataService', 'PosService'];
