export default class EmailsController {
    constructor($scope, $state, AuthService, EmailsService, DataService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, $filter) {
        if (!AuthService.isGranted('ROLE_ADMIN')) {
            AuthService.logout();
        }
        this.EmailsService = EmailsService;
        this.$scope = $scope;
        this.$state = $state;
        this.Flash = Flash;
        this.$scope.newEmail = {};
        this.$scope.editableFields = {};
        this.emailId = $stateParams.emailId || null;
        this.NgTableParams = NgTableParams;
        this.DataService = DataService;
        this.ParamsMap = ParamsMap;
        this.EditableMap = EditableMap;
        this.$q = $q;
        this.$scope.newEmails = {};
        this.$filter = $filter;
        this.config = DataService.getConfig();
        this.loaderStates = {
            translationList: true,
            translationDetails: true,
            coverLoader: true
        };
    }


    previewEmail(title, data){
        let wH = window.innerHeight;
        let wW = window.innerWidth;
        let previewWindow =
            window.open("", title, "width=" + wW*0.8 + ",height" + wH*0.8 + ",top=" + wH*0.1  + ",left=" + wW*0.1);
        previewWindow.document.write(data);
    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage
        }, {
            getData: function (params) {
                let dfd = self.$q.defer();
                self.loaderStates.translationList = true;

                self.EmailsService.getEmailsList(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.emails = res;
                            params.total(res.total);
                            self.loaderStates.translationList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.resolve(res);
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_translations.error');
                            self.Flash.create('danger', message);
                            self.loaderStates.translationList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

    getEmailsData() {
        let self = this;
        self.loaderStates.translationDetails = true;

        if (self.emailId) {
            self.EmailsService.getEmail(self.emailId)
                .then(
                    res => {
                        self.$scope.email = res;
                        self.$scope.editableFields = res;
                        self.loaderStates.translationDetails = false;
                        self.loaderStates.coverLoader = false;
                    },
                    () => {
                        let message = self.$filter('translate')('xhr.get_translation.error');
                        self.Flash.create('danger', message);
                        self.loaderStates.translationDetails = false;
                        self.loaderStates.coverLoader = false;
                    }
                )
        } else {
            self.$state.go('admin.emails');
            let message = self.$filter('translate')('xhr.get_translation.no_id');
            self.Flash.create('warning', message);
            self.loaderStates.translationDetails = false;
            self.loaderStates.coverLoader = false;
        }
    }

    editEmails(editedEmail) {
        let self = this;
        self.EmailsService.putEmail(self.emailId, {
            key: editedEmail.entity.key,
            subject: editedEmail.entity.subject,
            content: editedEmail.entity.content,
            sender_name: editedEmail.entity.sender_name,
            sender_email: editedEmail.entity.sender_email
        })
            .then(
                res => {
                    let message = self.$filter('translate')('xhr.put_translations.success');
                    self.Flash.create('success', message);
                    self.$state.go('admin.emails')
                },
                res => {
                    let message = self.$filter('translate')('xhr.put_translations.error');
                    self.Flash.create('danger', message);
                }
            )
    }
}

EmailsController.$inject = ['$scope', '$state', 'AuthService', 'EmailsService', 'DataService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', '$stateParams', 'EditableMap', '$filter'];