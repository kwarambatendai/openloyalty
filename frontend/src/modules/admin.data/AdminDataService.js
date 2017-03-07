export default class AdminDataService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getAdminData(Id) {
        return this.Restangular.one('admin').one('data').get();
    }

    putAdminData(edited) {
        let self = this;
        let data = {};
        data.firstName = edited.firstName;
        data.lastName = edited.lastName;
        data.email = edited.email;
        data.phone = edited.phone;

        return edited.customPUT({admin: self.Restangular.stripRestangular(data)});
    }
    changePassword(password) {
        let self = this;

        return this.Restangular.one('admin').one('password').one('change').customPOST({currentPassword: password.currentPassword, plainPassword: password.plainPassword});
    }
}

AdminDataService.$inject = ['Restangular', 'EditableMap'];