export default class EmailsService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getEmailsList(params) {
        return this.Restangular.one('settings').all('emails').getList(params);
    }

    getEmail(id) {
        return this.Restangular.one('settings').one('emails').one(id).get();
    }

    putEmail(emailId, edited) {
        let self = this;
        edited = self.Restangular.stripRestangular(edited);

        return self.Restangular.one('settings').one('emails', emailId).customPUT({email: edited});
    }
}

EmailsService.$inject = ['Restangular', 'EditableMap'];