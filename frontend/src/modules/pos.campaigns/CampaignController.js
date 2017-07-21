export default class CampaignController {
    constructor($scope, $state, $stateParams, AuthService, CampaignService, Flash, EditableMap, NgTableParams, ParamsMap, $q, Validation, $filter) {
        if (!AuthService.isGranted('ROLE_SELLER')) {
            $state.go('seller-login')
        }
        this.$scope = $scope;
        this.$scope.newCampaign = {};
        this.$scope.showCompany = false;
        this.$scope.showAddress = false;
        this.$state = $state;
        this.AuthService = AuthService;
        this.CampaignService = CampaignService;
        this.Flash = Flash;
        this.EditableMap = EditableMap;
        this.campaignId = $stateParams.campaignId || null;
        this.NgTableParams = NgTableParams;
        this.ParamsMap = ParamsMap;
        this.$q = $q;
        this.Validation = Validation;
        this.$filter = $filter;
        this.loaderVisible = {
            campaigns: true,
            singleCampaign: true
        }
    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({}, {
            getData: function (params) {
                let dfd = self.$q.defer();

                self.CampaignService.getCampaigns(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.campaigns = res;
                            self.loaderVisible.campaigns = false;
                            params.total(res.total);
                            dfd.resolve(res);
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_campaigns.error');
                            self.Flash.create('danger', message);
                            dfd.reject();
                            self.loaderVisible.campaigns = false;
                        }
                    );

                return dfd.promise;
            }
        });
    }

    getCampaignData() {
        let self = this;

        if (self.campaignId) {
            self.CampaignService.getCampaign(self.campaignId)
                .then(
                    res => {
                        self.$scope.campaign = res;
                        self.$scope.editableFields = self.EditableMap.humanizeCampaign(res);
                        if (self.$scope.editableFields.levels && self.$scope.editableFields.levels.length) {
                            let levels = self.$scope.editableFields.levels;
                            let levelsArray = [];
                            for (let i in levels) {
                                let level = _.find(self.levels, {id: levels[i]});
                            }
                            self.loaderVisible.singleCampaign = false;
                        }
                        if (self.$scope.editableFields.segments && self.$scope.editableFields.segments.length) {
                            let segments = self.$scope.editableFields.segments;
                            let segmentsArray = [];
                            for (let i in segments) {
                                let segment = _.find(self.segments, {id: segments[i]});
                            }
                            self.loaderVisible.singleCampaign = false;
                        }
                    },
                    () => {
                        let message = self.$filter('translate')('xhr.get_campaign.error');
                        self.Flash.create('danger', message);
                    }
                )
        } else {
            self.$state.go('admin.campaign-list');
            let message = self.$filter('translate')('xhr.get_campaign.no_id');
            self.Flash.create('warning', message);
        }
    }
}

CampaignController.$inject = ['$scope', '$state', '$stateParams', 'AuthService', 'CampaignService', 'Flash', 'EditableMap', 'NgTableParams', 'ParamsMap', '$q', 'Validation', '$filter'];