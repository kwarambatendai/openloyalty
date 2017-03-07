export default class UserService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getUsers(params) {
        if(!params) {
            params = {};
        }

        return this.Restangular.all('admin').getList(params);
    }

    getUser(userId) {
        return this.Restangular.one('admin').one('data', userId).get();

    }

    putUser(edited, full) {
        let id = edited.id;
        let self = this;
        let data = {};
        data.firstName = edited.firstName;
        data.lastName = edited.lastName;
        data.email = edited.email;
        data.phone = edited.phone;
        if (full) {
            data.plainPassword = edited.plainPassword;
            data.apiKey = edited.apiKey;
            data.external = edited.external;
            data.isActive = edited.isActive;
        }

        return this.Restangular.one('admin').one('data').one(id).customPUT({admin: self.Restangular.stripRestangular(data)});
    }

    postUser(user) {
        let self = this;
        let data = {};
        data.firstName = user.firstName;
        data.lastName = user.lastName;
        data.email = user.email;
        data.phone = user.phone;
        data.plainPassword = user.plainPassword;
        data.apiKey = user.apiKey;
        data.external = user.external;
        data.isActive = user.isActive;

        return this.Restangular.one('admin').one('data').customPOST({admin: self.Restangular.stripRestangular(data)});
    }
}

UserService.$inject = ['Restangular', 'EditableMap'];