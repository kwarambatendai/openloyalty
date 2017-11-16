/**
 * Describes Admin CampaignController
 * @class CampaignController
 * @constructor
 */
export default class CampaignController {
    /**
     * @param {Object} $scope
     * @param {Object} $state
     * @param {Object} $stateParams
     * @param {AuthService} AuthService
     * @param {CampaignService} CampaignService
     * @param {Object} Flash
     * @param {EditableMap} EditableMap
     * @param {Object} NgTableParams
     * @param {ParamsMap} ParamsMap
     * @param {Object} $q
     * @param {Object} Validation
     * @param {Object} $filter
     * @param {SegmentService} SegmentService
     * @param {Object} LevelService
     * @param {DataService} DataService
     * @method constructor
     */
    constructor($scope, $state, $stateParams, AuthService, CampaignService, Flash, EditableMap, NgTableParams, ParamsMap, $q, Validation, $filter, SegmentService, LevelService, DataService) {
        if (!AuthService.isGranted('ROLE_ADMIN')) {
            $state.go('admin-login')
        }
        this.$scope = $scope;
        this.$state = $state;
        this.$stateParams = $stateParams;

        this.SegmentService = SegmentService;
        this.LevelService = LevelService;
        this.AuthService = AuthService;
        this.DataService = DataService;
        this.CampaignService = CampaignService;
        this.EditableMap = EditableMap;
        this.ParamsMap = ParamsMap;
        this.Validation = Validation;

        this.Flash = Flash;
        this.NgTableParams = NgTableParams;
        this.$q = $q;
        this.$filter = $filter;
    }

    /**
     * Initial method
     *
     * @method $onInit
     */
    $onInit() {
        this.loaderStates = {
            campaignList: true,
            campaignDetails: true,
            campaignCustomerList: true,
            coverLoader: true
        };
        this.campaignId = this.$stateParams.campaignId || null;
        this.$scope.campaignName = this.$stateParams.campaignName || false;
        this.$scope.newCampaign = {};
        this.$scope.showCompany = false;
        this.$scope.showAddress = false;
        this.$scope.fileValidate = this.CampaignService.storedFileError;
        this.segments = null;
        this.levels = null;
        this.config = this.DataService.getConfig();
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
        this.reward = [
            {
                name: this.$filter('translate')('campaign.discount_code'),
                type: 'discount_code'
            },
            {
                name: this.$filter('translate')('campaign.event_code'),
                type: 'event_code'
            },

            {
                name: this.$filter('translate')('campaign.free_delivery_code'),
                type: 'free_delivery_code'
            },
            {
                name: this.$filter('translate')('campaign.gift_code'),
                type: 'gift_code'
            },
            {
                name: this.$filter('translate')('campaign.value_code'),
                type: 'value code'
            }
        ];
        this.egCoupon = ['Example_coupon'];
        this.rewardConfig = {
            valueField: 'type',
            labelField: 'name',
            create: false,
            sortField: 'name',
            maxItems: 1,
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
            valueField: 'type',
            labelField: 'name',
            create: false,
            sortField: 'name',
            maxItems: 1
        };
        this.couponsConfig = {
            delimiter: ',',
            persist: false,
            plugins: ['remove_button'],
            create: function (input) {
                return {
                    value: input,
                    text: input
                }
            }
        };


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

    /**
     * creates NgTable instances
     *
     * @method getData
     */
    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage
        }, {
            getData: function (params) {
                let dfd = self.$q.defer();
                self.loaderStates.campaignList = true;
                self.CampaignService.getCampaigns(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.campaigns = res;
                            self.CampaignService.setStoredCampaigns(res);
                            self.loaderStates.campaignList = false;
                            self.loaderStates.coverLoader = false;
                            params.total(res.total);
                            dfd.resolve(res)
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_campaigns.error');
                            self.loaderStates.campaignList = false;
                            self.loaderStates.coverLoader = false;
                            self.Flash.create('danger', message);
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

    /**
     * Sets campaign state
     *
     * @param {Boolean} active
     * @param {Integer} campaignId
     * @method setCampaignState
     */
    setCampaignState(active, campaignId) {
        let self = this;

        self.CampaignService.setCampaignState(active, campaignId)
            .then(
                () => {
                    let message = self.$filter('translate')('xhr.post_activate_campaign.success');
                    self.Flash.create('success', message);
                    self.tableParams.reload();
                },
                () => {
                    let message = self.$filter('translate')('xhr.post_activate_campaign.error');
                    self.Flash.create('danger', message);
                }
            )
    }

    /**
     * Obtains campaign data
     *
     * @method getCampaignData
     */
    getCampaignData() {
        let self = this;

        if (self.campaignId) {
            self.dataPromise.then(self._getCampaign())
        } else {
            self.$state.go('admin.campaign-list');
            let message = self.$filter('translate')('xhr.get_campaign.no_id');
            self.Flash.create('warning', message);
        }
    }

    /**
     * Adds new campaign
     *
     * @param {Object} newCampaign
     * @method addCampaign
     */
    addCampaign(newCampaign) {
        let self = this;

        self.CampaignService.postCampaign(newCampaign)
            .then(
                res => {
                    if (self.$scope.campaignFile) {
                        self.$scope.fileValidate = {};

                        self.CampaignService.postCampaignImage(res.campaignId, self.$scope.campaignFile)
                            .then(
                                res2 => {
                                    self.$state.go('admin.single-campaign', {campaignId: res.campaignId});
                                    let message = self.$filter('translate')('xhr.post_campaign.success');
                                    self.Flash.create('success', message);
                                }
                            )
                            .catch(
                                err => {
                                    self.$scope.fileValidate = self.Validation.mapSymfonyValidation(err.data);
                                    self.CampaignService.storedFileError = self.$scope.fileValidate;

                                    let message = self.$filter('translate')('xhr.post_campaign.warning');
                                    self.Flash.create('warning', message);

                                    self.$state.go('admin.edit-campaign', {campaignId: res.campaignId})
                                }
                            );

                    } else {
                        self.$state.go('admin.campaign-list');
                        let message = self.$filter('translate')('xhr.post_campaign.success');
                        self.Flash.create('success', message);
                    }
                },
                res => {
                    self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                    let message = self.$filter('translate')('xhr.post_campaign.error');
                    self.Flash.create('danger', message);
                }
            )
    }

    /**
     * Deletes photo
     *
     * @method deletePhoto
     */
    deletePhoto() {
        let self = this;

        this.CampaignService.deleteCampaignImage(this.$stateParams.campaignId)
            .then(
                res => {
                    self.$scope.campaignFilePath = false;
                    let message = self.$filter('translate')('xhr.delete_campaign_image.success');
                    self.Flash.create('success', message);
                }
            )
            .catch(
                err => {
                    self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                    let message = self.$filter('translate')('xhr.delete_campaign_image.error');
                    self.Flash.create('danger', message);
                }
            )
    }

    /**
     * Edits campaign
     *
     * @param editedCampaign
     * @method editCampaign
     */
    editCampaign(editedCampaign) {
        let self = this;

        self.CampaignService.putCampaign(editedCampaign)
            .then(
                res => {
                    if (self.$scope.campaignFile) {
                        self.$scope.fileValidate = {};

                        self.CampaignService.postCampaignImage(self.$stateParams.campaignId, self.$scope.campaignFile)
                            .then(
                                res2 => {
                                    self.CampaignService.storedFileError = {};
                                    self.$state.go('admin.single-campaign', {campaignId: res.campaignId});

                                    let message = self.$filter('translate')('xhr.put_campaign.success');
                                    self.Flash.create('success', message);
                                    self.loaderStates.coverLoader = false;
                                }
                            )
                            .catch(
                                err => {
                                    self.$scope.fileValidate = self.Validation.mapSymfonyValidation(err.data);
                                    let message = self.$filter('translate')('xhr.put_campaign.error');
                                    self.Flash.create('danger', message);
                                    self.loaderStates.coverLoader = false;
                                }
                            );

                    } else {
                        self.$state.go('admin.single-campaign', {campaignId: res.campaignId});
                        let message = self.$filter('translate')('xhr.put_campaign.success');
                        self.Flash.create('success', message);
                        self.loaderStates.campaignDetails = false;
                        self.loaderStates.coverLoader = false;
                    }
                },
                res => {
                    self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                    let message = self.$filter('translate')('xhr.put_campaign.error');
                    self.Flash.create('danger', message);
                    self.loaderStates.campaignDetails = false;
                    self.loaderStates.coverLoader = false;
                }
            )
    }

    /**
     * Obtains customers for campaign
     *
     * @method getCustomersForCampaign
     */
    getCustomersForCampaign() {
        let self = this;

        if (self.campaignId) {
            self.customersTableParams = new self.NgTableParams({
                count: self.config.perPage
            }, {
                getData: function (params) {
                    let dfd = self.$q.defer();
                    self.loaderStates.campaignCustomerList = true;

                    self.CampaignService.getVisibleCustomers(self.campaignId, self.ParamsMap.params(params.url()))
                        .then(
                            res => {
                                self.$scope.customers = res;
                                params.total(res.total);
                                dfd.resolve(res)
                                self.loaderStates.campaignCustomerList = false;
                                self.loaderStates.coverLoader = false;
                            },
                            () => {
                                let message = self.$filter('translate')('xhr.get_customers_for_campaign.error');
                                self.Flash.create('danger', message);
                                dfd.reject();
                                self.loaderStates.campaignCustomerList = false;
                                self.loaderStates.coverLoader = false;
                            }
                        );

                    return dfd.promise;
                }
            });
        } else {
            self.$state.go('admin.campaign-list');
            let message = self.$filter('translate')('xhr.get_customers_for_campaign.no_id');
            self.Flash.create('warning', message);
            self.loaderStates.campaignCustomerList = false;
        }
    }

    /**
     * Generating photo route
     *
     * @method generatePhotoRoute
     * @returns {string}
     */
    generatePhotoRoute() {
        return this.DataService.getConfig().apiUrl + '/campaign/' + this.$stateParams.campaignId + '/photo'
    }

    /**
     * Obtain all campaign data
     *
     * @method _getCampaign
     * @private
     */
    _getCampaign() {
        let self = this;

        self.CampaignService.getCampaign(self.campaignId)
            .then(
                res => {
                    self.$scope.campaign = res;
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
                    self.loaderStates.campaignDetails = false;
                },
                () => {
                    let message = self.$filter('translate')('xhr.get_campaign.error');
                    self.Flash.create('danger', message);
                    self.loaderStates.campaignDetails = false;
                }
            );

        self.CampaignService.getCampaignImage(self.campaignId)
            .then(
                res => {
                    self.$scope.campaignFilePath = true;
                }
            )
            .catch(
                err => {
                    self.$scope.campaignFilePath = false;
                }
            );
    }
}

CampaignController.$inject = ['$scope', '$state', '$stateParams', 'AuthService', 'CampaignService', 'Flash', 'EditableMap', 'NgTableParams', 'ParamsMap', '$q', 'Validation', '$filter', 'SegmentService', 'LevelService', 'DataService'];
