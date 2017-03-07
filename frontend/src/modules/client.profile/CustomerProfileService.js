export default class CustomerProfileService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getCustomer(id) {
        return this.Restangular.one('customer', id).get();
    }

    putCustomer(editedCustomer) {
        let self = this;

        return editedCustomer.customPUT({customer: self.EditableMap.customer(editedCustomer)});
    }

    changePassword(password) {
        let self = this;

        return this.Restangular.one('customer').one('password').one('change').customPOST({currentPassword: password.currentPassword, plainPassword: password.plainPassword});
    }
}

CustomerProfileService.$inject = ['Restangular', 'EditableMap'];