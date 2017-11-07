export default class EarningRuleController {
    constructor($scope, $state, AuthService, EarningRuleService, SegmentService, LevelService, DataService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, Validation, $filter) {
        if (!AuthService.isGranted('ROLE_ADMIN')) {
            AuthService.logout();
        }
        this.$scope = $scope;
        this.EarningRuleService = EarningRuleService;
        this.SegmentService = SegmentService;
        this.LevelService = LevelService;
        this.$state = $state;
        this.Flash = Flash;
        this.$scope.newEarningRule = {};
        this.$scope.editableFields = {};
        this.earningRuleId = $stateParams.earningRuleId || null;
        this.NgTableParams = NgTableParams;
        this.DataService = DataService;
        this.promotedEvents = this.DataService.getAvailablePromotedEvents();
        this.referralTypes = this.DataService.getAvailableReferralTypes();
        this.referralEvents = this.DataService.getAvailableReferralEvents();
        this.availableEarningRuleLimitPeriods = this.DataService.getAvailableEarningRuleLimitPeriods();
        this.ParamsMap = ParamsMap;
        this.EditableMap = EditableMap;
        this.$q = $q;
        this.Validation = Validation;
        this.$filter = $filter;
        this.$scope.egSkus = ['SKU123'];
        this.config = DataService.getConfig();
        this.segments = null;
        this.levels = null;
        this.$scope.skusConfig = {
            delimiter: ';',
            persist: false,
            create: true,
            plugins: ['remove_button'],
        };

        this.target = [
            {
                name: this.$filter('translate')('global.segment'),
                type: 'segment'
            },
            {
                name: this.$filter('translate')('global.level'),
                type: 'level'
            }
        ];
        this.typeConfig = {
            valueField: 'value',
            labelField: 'name',
            create: false,
            sortField: 'name',
            maxItems: 1,
            onChange: this.eventTypeChanged.bind(this)
        };
        this.levelsConfig = {
            valueField: 'id',
            labelField: 'name',
            create: false,
            plugins: ['remove_button'],
            sortField: 'name'
        };
        this.segmentsConfig = {
            valueField: 'segmentId',
            labelField: 'name',
            create: false,
            plugins: ['remove_button'],
            sortField: 'name'
        };
        this.targetConfig = {
            valueField: 'type',
            labelField: 'name',
            create: false,
            sortField: 'name',
            maxItems: 1
        };
        this.activeConfig = {
            valueField: 'value',
            labelField: 'name',
            create: false,
            sortField: 'name',
            maxItems: 1,
        };
        this.promotedEventsConfig = {
            valueField: 'code',
            labelField: 'name',
            create: false,
            sortField: 'name',
            maxItems: 1,
        };
        this.referralTypesConfig = {
            valueField: 'code',
            labelField: 'name',
            create: false,
            sortField: 'name',
            maxItems: 1,
        };
        this.referralEventsConfig = {
            valueField: 'code',
            labelField: 'name',
            create: false,
            sortField: 'name',
            maxItems: 1,
        };
        this.availableEarningRuleLimitPeriodsConfig = {
            valueField: 'code',
            labelField: 'name',
            create: false,
            sortField: 'name',
            maxItems: 1,
        };
        this.active = [
            {
                name: this.$filter('translate')('global.active'),
                value: 1
            },
            {
                name: this.$filter('translate')('global.inactive'),
                value: 0
            }
        ];
        this.types = [
            {
                name: this.$filter('translate')('earning_rule.types.points'),
                value: "points"
            },
            {
                name: this.$filter('translate')('earning_rule.types.event'),
                value: "event"
            },
            {
                name: this.$filter('translate')('earning_rule.types.custom_event'),
                value: "custom_event"
            },
            {
                name: this.$filter('translate')('earning_rule.types.product_purchase'),
                value: "product_purchase"
            },
            {
                name: this.$filter('translate')('earning_rule.types.multiply_for_product'),
                value: "multiply_for_product"
            },
            {
                name: this.$filter('translate')('earning_rule.types.referral'),
                value: "referral"
            }
        ];

        this.loaderStates = {
            earningRuleDetails: true,
            earningRuleList: true,
            coverLoader: true
        }

        let segmentPromise = this.SegmentService.getActiveSegments({perPage: 1000})
            .then(
                res => {
                    this.segments = res;
                }
            );

        let levelPromise = this.LevelService.getLevels()
            .then(
                res => {
                    this.levels = res;
                }
            );

        this.dataPromise = this.$q.all([segmentPromise, levelPromise]);

    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage
        }, {
            getData: function (params) {
                let dfd = self.$q.defer();

                self.loaderStates.earningRuleList = true;
                self.EarningRuleService.getEarningRules(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.earningRules = res;
                            params.total(res.total);
                            self.loaderStates.earningRuleList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.resolve(res);
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_earning_rules.error');
                            self.Flash.create('danger', message);
                            self.loaderStates.earningRuleList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

    eventTypeChanged(type) {
        if ((type == 'event' || type == 'custom_event' || type == 'referral') && this.$scope.newEarningRule) {
            this.$scope.newEarningRule.eventName = null;
        }
    }

    getEarningRuleData() {
        let self = this;

        if (self.earningRuleId) {
            self.dataPromise.then(self._getEarningRule())
        } else {
            self.$state.go('admin.earning-rule-list');
            let message = self.$filter('translate')('xhr.get_campaign.no_id');
            self.Flash.create('warning', message);
        }
    }

    _getEarningRule() {
        let self = this;
        self.loaderStates.earningRuleDetails = true;

        if (self.earningRuleId) {
            self.EarningRuleService.getEarningRule(self.earningRuleId)
                .then(
                    res => {
                        self.$scope.earningRule = res;
                        self.$scope.editableFields = self.EditableMap.humanizeCampaign(res);
                        if (self.$scope.editableFields.levels && self.$scope.editableFields.levels.length) {
                            let levels = self.$scope.editableFields.levels;
                            for (let i in levels) {
                                let level = _.find(self.levels, {id: levels[i]});
                            }

                        }
                        if (self.$scope.editableFields.segments && self.$scope.editableFields.segments.length) {
                            let segments = self.$scope.editableFields.segments;
                            for (let i in segments) {
                                let segment = _.find(self.segments, {id: segments[i]});
                            }

                        }
                        self.$scope.editableFields = self.EditableMap.humanizeEarningRuleFields(res);
                        self.loaderStates.earningRuleDetails = false;
                    },
                    () => {
                        let message = self.$filter('translate')('xhr.get_earning_rule.error');
                        self.Flash.create('danger', message);
                        self.loaderStates.earningRuleDetails = false;
                    }
                )
        } else {
            self.$state.go('admin.earning-rule-list');
            let message = self.$filter('translate')('xhr.get_earning_rule.no_id');
            self.Flash.create('warning', message);
            self.loaderStates.earningRuleDetails = false;
        }
    }

    editEarningRule(editedEarningRule) {
        let self = this;
        self.loaderStates.earningRuleDetails = true;
        self.EarningRuleService.putEarningRule(self.earningRuleId, self.EditableMap.newEarningRule(editedEarningRule, true))
            .then(
                res => {
                    let message = self.$filter('translate')('xhr.put_earning_rule.success');
                    self.Flash.create('success', message);
                    self.$state.go('admin.earning-rule-list');
                    self.loaderStates.earningRuleDetails = false;
                },
                res => {
                    self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                    let message = self.$filter('translate')('xhr.put_earning_rule.error');
                    self.Flash.create('danger', message);
                    self.loaderStates.earningRuleDetails = false;
                }
            )
    }

    addEarningRule(newEarningRule) {
        let self = this;

        self.EarningRuleService.postEarningRule(self.EditableMap.newEarningRule(newEarningRule))
            .then(
                res => {
                    let message = self.$filter('translate')('xhr.post_earning_rule.success');
                    self.Flash.create('success', message);
                    self.loaderStates.earningRuleDetails = false;
                    self.$state.go('admin.earning-rule-list')
                },
                res => {
                    self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                    let message = self.$filter('translate')('xhr.put_earning_rule.error');
                    self.Flash.create('danger', message);
                    self.loaderStates.earningRuleDetails = false;
                }
            );
    }

    addExcludedSKU(edit) {
        if (edit) {
            this.$scope.editableFields.excludedSKUs.push('')
        } else {
            this.$scope.newEarningRule.excludedSKUs.push('')
        }

    }

    addExcludedLabel(edit) {
        if (edit) {
            if (!(this.$scope.editableFields.excludedLabels instanceof Array)) {
                this.$scope.editableFields.excludedLabels = [];
            }
            this.$scope.editableFields.excludedLabels.push({
                key: '',
                value: ''
            })
        } else {
            this.$scope.newEarningRule.excludedLabels.push({
                key: '',
                value: ''
            })
        }
    }

    removeExcludedSKU(index, edit) {
        let self = this;
        let earningRule;

        if (!edit) {
            earningRule = self.$scope.newEarningRule;
        } else {
            earningRule = self.$scope.editableFields;
        }

        earningRule.excludedSKUs = _.difference(earningRule.excludedSKUs, [earningRule.excludedSKUs[index]])
    }

    removeExcludedLabel(index, edit) {
        let self = this;
        let earningRule;

        if (!edit) {
            earningRule = self.$scope.newEarningRule;
        } else {
            earningRule = self.$scope.editableFields;
        }

        earningRule.excludedLabels = _.difference(earningRule.excludedLabels, [earningRule.excludedLabels[index]])
    }

    setRuleState(state, ruleId) {
        let self = this;

        self.EarningRuleService.postActivateRule(state, ruleId)
            .then(
                () => {
                    let message = self.$filter('translate')('xhr.post_activate_rule.success');
                    self.Flash.create('success', message);
                    self.tableParams.reload();
                },
                () => {
                    let message = self.$filter('translate')('xhr.post_activate_rule.error');
                    self.Flash.create('danger', message);
                }
            )

    }
}

EarningRuleController.$inject = ['$scope', '$state', 'AuthService', 'EarningRuleService', 'SegmentService', 'LevelService', 'DataService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', '$stateParams', 'EditableMap', 'Validation', '$filter'];