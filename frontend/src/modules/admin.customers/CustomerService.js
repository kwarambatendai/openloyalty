export default class CustomerService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getCustomers(params) {
        if(!params) {
            params = {};
        }

        return this.Restangular.all('customer').getList(params);
    }

    postCustomer(newCustomer) {
        return this.Restangular.one('customer').one('register').customPOST({customer:newCustomer})
    }

    getCustomer(customerId) {
        return this.Restangular.one('customer', customerId).get();
    }

    getCustomerStatus(customerId) {
        return this.Restangular.one('admin').one('customer', customerId).one('status').get();
    }

    getCustomerTransactions(params, customerId) {
        params.customerId = customerId;

        return this.Restangular.all('transaction').getList(params);
    }

    getCustomerTransfers(params, customerId) {
        params.customerId = customerId;

        return this.Restangular.one('points').all('transfer').getList(params);
    }

    getCustomerAvailableCampaigns(params, customerId) {
        params.customerId = customerId;

        return this.Restangular.one('admin').one('customer', customerId).one('campaign').all('available').getList(params);
    }

    getCustomerBoughtCampaigns(params, customerId) {
        params.customerId = customerId;
        params.includeDetails = true;

        return this.Restangular.one('admin').one('customer', customerId).one('campaign').all('bought').getList(params);
    }

    putCustomer(editedCustomer) {
        let self = this;

        return editedCustomer.customPUT({customer: self.EditableMap.customer(editedCustomer)});
    }

    postLevel(editedCustomer, levelId) {
        return editedCustomer.customPOST({levelId: levelId}, 'level')
    }

    postPos(editedCustomer, posId) {
        return editedCustomer.customPOST({posId: posId}, 'pos')
    }

    deactivateCustomer(customerId) {
        return this.Restangular.one('admin').one('customer', customerId).one('deactivate').customPOST();
    }
    activateCustomer(customerId) {
        return this.Restangular.one('admin').one('customer', customerId).one('activate').customPOST();
    }

    postUsage(customerId, campaignId, code, usage) {
        return this.Restangular.one('admin').one('customer').one(customerId).one('campaign').one(campaignId).one('coupon').one(code).customPOST({used: usage});
    }
}

CustomerService.$inject = ['Restangular', 'EditableMap'];