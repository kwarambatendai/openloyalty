export default class SellerCustomerService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    search(data) {
        return this.Restangular
            .one('pos')
            .one('search')
            .one('customer')
            .customPOST({search: data})
    }

    getCustomers(params) {
        if (!params) {
            params = {};
        }

        return this.Restangular.all('customer').getList(params);
    }

    postCustomer(newCustomer) {
        return this.Restangular.one('seller').one('customer').one('register').customPOST({customer: newCustomer})
    }

    getLevels(params) {
        return this.Restangular.one('seller').all('level').getList(params);
    }

    getPosList(params) {
        return this.Restangular.one('seller').all('pos').getList(params);
    }

    getCustomer(customerId) {
        return this.Restangular.one('customer', customerId).get();
    }

    putCustomer(editedCustomer) {
        let self = this;

        return editedCustomer.customPUT({customer: self.EditableMap.customer(editedCustomer, true)});
    }

    postLevel(editedCustomer, levelId) {
        return editedCustomer.customPOST({levelId: levelId}, 'level')
    }

    postPos(editedCustomer, posId) {
        return editedCustomer.customPOST({posId: posId}, 'pos')
    }

    getLevel(levelId) {
        return this.Restangular.one('seller').one('level', levelId).get();
    }

    getPos(pos) {
        return this.Restangular.one('seller').one('pos', pos).get();
    }

    getCustomerTransactions(params, customerId) {
        params.customerId = customerId;

        return this.Restangular.one('seller').all('transaction').getList(params);
    }

    getCustomerTransfers(params, customerId) {
        params.customerId = customerId;

        return this.Restangular.one('seller').one('points').all('transfer').getList(params);
    }

    getCustomerAvailableCampaigns(params, customerId) {
        params.customerId = customerId;

        return this.Restangular.one('seller').one('customer', customerId).one('campaign').all('available').getList(params);
    }

    getCustomerBoughtCampaigns(params, customerId) {
        params.customerId = customerId;
        params.includeDetails = true;

        return this.Restangular.one('seller').one('customer', customerId).one('campaign').all('bought').getList(params);
    }

    getCustomerStatus(customerId) {
        return this.Restangular.one('seller').one('customer', customerId).one('status').get();
    }

    deactivateCustomer(customerId) {
        return this.Restangular.one('seller').one('customer', customerId).one('deactivate').customPOST();
    }
}

SellerCustomerService.$inject = ['Restangular', 'EditableMap'];
