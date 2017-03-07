/**
 * Defines service to call backend campaign API
 *
 * @class CampaignService
 * @constructor
 */
export default class CampaignService {
    /**
     * @method constructor
     * @param {Object} Restangular
     * @param {EditableMap} EditableMap
     */
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
        this.campaigns = null;
        this._campaignFileError = {};
    }

    /**
     * Gets stored campaign file error
     *
     * @method storedFileError
     */
    get storedFileError() {
        return this._campaignFileError
    }

    /**
     * Sets stored campaign file error
     *
     * @method storedFileError
     */
    set storedFileError(error) {
        this._campaignFileError = error;
    }

    /**
     * Stores campaign in service
     *
     * @method setStoredCampaigns
     * @param campaigns
     */
    setStoredCampaigns(campaigns) {
        this.campaigns = campaigns;
    }

    /**
     * Returns stored campaign
     *
     * @method getStoredCampaigns
     * @returns {Object|null}
     */
    getStoredCampaigns() {
        return this.campaigns;
    }

    /**
     * Calls for campaign list
     *
     * @method getCampaigns
     * @param {Object} params
     * @returns {Promise}
     */
    getCampaigns(params = {}) {
        return this.Restangular
            .all('campaign')
            .getList(params);
    }

    /**
     * Calls to post new campaign
     *
     * @method postCampaign
     * @param newCampaign
     * @returns {Promise}
     */
    postCampaign(newCampaign) {
        let self = this;

        return this.Restangular
            .one('campaign')
            .customPOST({campaign: self.EditableMap.campaign(newCampaign)})
    }

    /**
     * Calls single campaign details
     *
     * @method getCampaign
     * @param {Integer} campaignId
     * @returns {Promise}
     */
    getCampaign(campaignId) {
        return this.Restangular.one('campaign', campaignId).get();
    }

    /**
     * Calls for post image to campaign
     *
     * @method postCampaignImage
     * @param {Integer} campaignId
     * @param {Object} data
     * @returns {Promise}
     */
    postCampaignImage(campaignId, data) {
        let fd = new FormData();

        fd.append('photo[file]', data);

        return this.Restangular
            .one('campaign', campaignId)
            .one('photo')
            .withHttpConfig({transformRequest: angular.identity})
            .customPOST(fd, '', undefined, {'Content-Type': undefined});
    }

    /**
     * Calls for campain image
     *
     * @method getCampaignImage
     * @param {Integer} campaignId
     * @returns {Promise}
     */
    getCampaignImage(campaignId) {
        return this.Restangular
            .one('campaign', campaignId)
            .one('photo')
            .get()
    }

    /**
     * Calls to remove campaign photo
     *
     * @method deleteCampaignImage
     * @param {Integer} campaignId
     * @returns {Promise}
     */
    deleteCampaignImage(campaignId) {
        return this.Restangular
            .one('campaign', campaignId)
            .one('photo')
            .remove()
    }

    /**
     * Calls to set campaign state
     *
     * @method setCampaignState
     * @param {Boolean} active
     * @param {Integer} campaignId
     * @returns {Promise}
     */
    setCampaignState(active, campaignId) {
        return this.Restangular
            .one('campaign')
            .one(campaignId)
            .one(active ? 'active' : 'inactive')
            .customPOST();
    }

    /**
     * Calls for edit campaign
     *
     * @method putCampaign
     * @param {Object} editedCampaign
     * @returns {Promise}
     */
    putCampaign(editedCampaign) {
        let self = this;

        return editedCampaign
            .customPUT({campaign: self.Restangular.stripRestangular(self.EditableMap.campaign(editedCampaign))});
    }

    /**
     * Calls for visible customers list
     *
     * @method getVisibleCustomers
     * @param campaignId
     * @param params
     * @returns {Promise}
     */
    getVisibleCustomers(campaignId, params = {}) {
        return this.Restangular
            .one('campaign')
            .one(campaignId)
            .one('customers')
            .all('visible')
            .getList(params);
    }

}

CampaignService.$inject = ['Restangular', 'EditableMap'];