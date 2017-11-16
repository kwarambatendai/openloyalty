export default class SettingsController {
    constructor($scope, SettingsService, Flash, DataService, $filter, Validation, $translate, TranslationService) {
        let self = this;
        this.$scope = $scope;
        this.Flash = Flash;
        this.SettingsService = SettingsService;
        this.DataService = DataService;
        this.TranslationService = TranslationService;
        this.$filter = $filter;
        this.Validation = Validation;
        this.$translate = $translate;

        this.$scope.refresh = false;
        this.$scope.languages = this.DataService.getLanguages();
        this.$scope.availableFrontendTranslations = this.DataService.getAvailableFrontendTranslations();
        this.$scope.timezones = this.DataService.getTimezones();
        this.$scope.countries = this.DataService.getCountries();
        this.$scope.currencies = this.DataService.getCurrencies();
        this.$scope.validate = {};
        this.currencyConfig = {
            valueField: 'code',
            labelField: 'name',
            create: false,
            sortField: 'name',
            searchField: 'name',
            maxItems: 1,
        };
        this.countryConfig = {
            valueField: 'code',
            labelField: 'name',
            create: false,
            sortField: 'name',
            searchField: 'name',
            maxItems: 1,
        };
        this.languageConfig = {
            valueField: 'code',
            labelField: 'name',
            create: false,
            sortField: 'name',
            searchField: 'name',
            maxItems: 1,
        };
        this.defaultFrontendTranslationsConfig = {
            valueField: 'code',
            labelField: 'name',
            create: false,
            sortField: 'name',
            searchField: 'name',
            maxItems: 1,
            onChange: function (value) {
                if (!this.defaultFrontendTranslationValue) {
                    this.defaultFrontendTranslationValue = value;
                }
                if (this.defaultFrontendTranslationValue != value) {
                    self.$scope.refresh = true;
                }
                this.defaultFrontendTranslationValue = value;
            }
        };
        this.timezoneConfig = {
            valueField: 'value',
            labelField: 'name',
            create: false,
            sortField: 'name',
            searchField: 'name',
            maxItems: 1,
        };
        this.fieldConfig = {
            valueField: 'value',
            labelField: 'name',
            create: false,
            sortField: 'name',
            searchField: 'name',
            maxItems: 1,
        };
        this.tierConfig = {
            valueField: 'value',
            labelField: 'name',
            create: false,
            sortField: 'name',
            searchField: 'name',
            maxItems: 1,
        };
        this.fields = [
            {
                name: 'loyaltyCardNumber',
                value: 'loyaltyCardNumber'
            },
            {
                name: 'email',
                value: 'email'
            }
        ];
        this.tiers = [
            {
                name: 'points',
                value: 'points'
            },
            {
                name: 'transactions',
                value: 'transactions'
            }
        ];
        this.egSkus = ['SKU123'];
        this.skusConfig = {
            delimiter: ',',
            plugins: ['remove_button'],
            persist: false,
            create: function (input) {
                return {
                    value: input,
                    text: input
                }
            }
        };

        this.loaderStates = {
            adminSettings: true,
            coverLoader: true
        };

        // this.$scope.frontValidate = {
        //     currency: '@assert:not_blank',
        //     timezone: '@assert:not_blank',
        //     language: '@assert:not_blank',
        //     programName: '@assert:not_blank',
        //     programUrl: '@assert:not_blank',
        //     programConditionsUrl: '@assert:not_blank',
        //     programFaqUrl: '@assert:not_blank',
        //     programPointsSingular: '@assert:not_blank',
        //     programPointsPlural: '@assert:not_blank',
        //     helpEmailAddress: '@assert:not_blank',
        //     allTimeActive: '@assert:or_field:pointsDaysActive',
        //     pointsDaysActive: '@assert:or_field:allTimeActive',
        //     tierAssignType: '@assert:not_blank',
        //
        // };
        // this.$scope.externalValidation = {
        //     priority: '@assert:not_blank',
        //     field: '@assert:not_blank'
        // }
    }

    getData() {
        let self = this;
        self.loaderStates.adminSettings = true;

        self.SettingsService.getSettingsData()
            .then(
                () => {
                    self.$scope.settings = self.SettingsService.getSettings();
                    self.$scope.settingsOld = angular.copy(self.$scope.settingsOld);
                    self.loaderStates.adminSettings = false;
                    self.loaderStates.coverLoader = false;
                },
                () => {
                    let message = self.$filter('translate')('xhr.get_settings.error');
                    self.Flash.create('danger', message);
                    self.loaderStates.adminSettings = false;
                    self.loaderStates.coverLoader = false;
                }
            )
    }

    editSettings() {
        let self = this;
        // let validateFields = angular.copy(self.$scope.frontValidate);
        // if (self.$scope.settings.customersIdentificationPriority) {
        //     validateFields.customersIdentificationPriority = {};
        //     for(let i = 0; i < self.$scope.settings.customersIdentificationPriority.length; i++) {
        //         validateFields.customersIdentificationPriority[i] = angular.copy(self.$scope.externalValidation)
        //     }
        // }
        //
        // let frontValidation = self.Validation.frontValidation(self.$scope.settings, validateFields);
        // if (_.isEmpty(frontValidation)) {
        self.SettingsService.postSettings(self.$scope.settings)
            .then(
                res => {
                    let message = self.$filter('translate')('xhr.put_settings.success');
                    self.Flash.create('success', message);
                    self.$scope.validate = {};
                    self.$scope.settings = res.settings;
                    self.TranslationService.removeStoredTranslations();
                    self.$translate.refresh();
                    self.$scope.settingsOld = angular.copy(self.$scope.settings);

                    if (self.$scope.refresh) {
                        window.location.reload(true);
                    }
                },
                (res) => {
                    self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                    let message = self.$filter('translate')('xhr.put_settings.error');
                    self.Flash.create('danger', message);
                }
            );
        // } else {
        //     let message = self.$filter('translate')('xhr.put_settings.error');
        //     self.Flash.create('danger', message);
        //     self.$scope.validate = frontValidation;
        // }
    }

    removeIdentificationPriority(index) {
        let self = this;

        self.$scope.settings.customersIdentificationPriority = _.difference(self.$scope.settings.customersIdentificationPriority, [self.$scope.settings.customersIdentificationPriority[index]])
    }

    addIdentificationPriority() {
        let self = this;
        if (!self.$scope.settings.customersIdentificationPriority) {
            self.$scope.settings.customersIdentificationPriority = []
        }

        self.$scope.settings.customersIdentificationPriority.push({})
    }

}

SettingsController.$inject = ['$scope', 'SettingsService', 'Flash', 'DataService', '$filter', 'Validation', '$translate', 'TranslationService'];
