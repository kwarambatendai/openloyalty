export default class CustomerCampaignService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getAvailable(params) {
        return this.Restangular.one('customer').one('campaign').all('available').getList(params);
    }

    getBought(params) {
        params.includeDetails = true;
        return this.Restangular.one('customer').one('campaign').all('bought').getList(params);
    }

    postBuy(campaignId) {
        return this.Restangular.one('customer').one('campaign').one(campaignId).one('buy').post();
    }

    postUsage(campaignId, code, usage) {
        return this.Restangular.one('customer').one('campaign').one(campaignId).one('coupon').one(code).customPOST({used: usage});
    }
}

CustomerCampaignService.$inject = ['Restangular', 'EditableMap'];