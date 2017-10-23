export default class TranslationsController {
    constructor($scope, $state, AuthService, TranslationsService, DataService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, Validation, $filter, $translate, TranslationService) {
        if (!AuthService.isGranted('ROLE_ADMIN')) {
            AuthService.logout();
        }
        this.TranslationsService = TranslationsService;
        this.TranslationService = TranslationService;
        this.$translate = $translate;
        this.$scope = $scope;
        this.$state = $state;
        this.Flash = Flash;
        this.$scope.newTranslation = {};
        this.$scope.editableFields = {};
        this.translationId = $stateParams.translationId || null;
        this.NgTableParams = NgTableParams;
        this.DataService = DataService;
        this.ParamsMap = ParamsMap;
        this.EditableMap = EditableMap;
        this.$q = $q;
        this.$scope.jsonOptions = {mode: 'code', change: this.validateData.bind(this)};
        this.$scope.newTranslations = {};
        this.Validation = Validation;
        this.$filter = $filter;
        this.config = DataService.getConfig();
        this.jsonEditor = null;
        this.$scope.validate = {};
        this.$scope.editorLoaded = function (e) {
            this.jsonEditor = e;
            this.validateData();
        }.bind(this);

        this.loaderStates = {
            translationList: true,
            translationDetails: true,
            coverLoader: true
        }
    }

    validateData() {
        try {
            this.jsonEditor.get();
            if (this.$scope.validate.content && this.$scope.validate.content.errors) {
                delete this.$scope.validate.content.errors[this.$scope.validate.content.errors.indexOf("admin.translations.content_error")];
            }
        } catch (e) {
            if (!this.$scope.validate.content) {
                this.$scope.validate.content = {};
            }
            this.$scope.validate.content.errors = ["admin.translations.content_error"];
        }
    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage
        }, {
            getData: function (params) {
                let dfd = self.$q.defer();
                self.loaderStates.translationList = true;

                self.TranslationsService.getTranslationsList(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.translations = res;
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

    getTranslationsData() {
        let self = this;
        self.loaderStates.translationDetails = true;

        if (self.translationId) {
            self.TranslationsService.getTranslation(self.translationId)
                .then(
                    res => {
                        self.$scope.translation = res;
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
            self.$state.go('admin.translations');
            let message = self.$filter('translate')('xhr.get_translation.no_id');
            self.Flash.create('warning', message);
            self.loaderStates.translationDetails = false;
            self.loaderStates.coverLoader = false;
        }
    }

    editTranslations() {
        let self = this;
        self.TranslationsService.putTranslation(self.translationId, {
            name: self.$scope.editableFields.name,
            content: self.$scope.editableFields.content
        })
            .then(
                res => {
                    let message = self.$filter('translate')('xhr.put_translations.success');
                    self.Flash.create('success', message);
                    self.$state.go('admin.translations');
                    this.DataService.getAvailableData();

                    self.TranslationService.removeStoredTranslations();
                    self.$translate.refresh();
                    window.location.reload(true);
                },
                res => {
                    self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                    let message = self.$filter('translate')('xhr.put_translations.error');
                    self.Flash.create('danger', message);
                }
            )
    }

    addTranslations(newTranslations) {
        let self = this;

        self.TranslationsService.postTranslation({name: newTranslations.name, content: newTranslations.content})
            .then(
                res => {
                    let message = self.$filter('translate')('xhr.post_translations.success');
                    self.Flash.create('success', message);
                    self.$state.go('admin.translations');
                    this.DataService.getAvailableData();
                },
                res => {
                    self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);

                    let message = self.$filter('translate')('xhr.post_translations.error');
                    self.Flash.create('danger', message);
                }
            )
    }
}

TranslationsController.$inject = ['$scope', '$state', 'AuthService', 'TranslationsService', 'DataService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', '$stateParams', 'EditableMap', 'Validation', '$filter', '$translate', 'TranslationService'];
