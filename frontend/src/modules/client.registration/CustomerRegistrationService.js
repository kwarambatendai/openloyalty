export default class CustomerRegistrationService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    postCustomer(newCustomer, invitationToken) {
        newCustomer = this.EditableMap.customer(newCustomer);
        if (invitationToken) {
            newCustomer.invitationToken = invitationToken;
        }

        return this.Restangular.one('customer').one('self_register').customPOST({customer:newCustomer})
    }

    postActivate(token) {
        return this.Restangular.one('customer').one('activate').one(token).customPOST({})
    }
}

CustomerRegistrationService.$inject = ['Restangular', 'EditableMap'];