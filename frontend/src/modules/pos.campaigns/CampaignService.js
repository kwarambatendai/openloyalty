export default class CampaignService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
        this.campaigns = null;
    }

    getCampaigns(params) {
        if(!params) {
            params = {};
        }

        return this.Restangular.one('seller').all('campaign').getList(params);
    }


    getCampaign(campaignId) {
        return this.Restangular.one('seller').one('campaign', campaignId).get();
    }
}

CampaignService.$inject = ['Restangular', 'EditableMap'];