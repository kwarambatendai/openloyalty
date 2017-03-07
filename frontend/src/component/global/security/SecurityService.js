export default class SecurityService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    postPasswordRequest(username) {
        return this.Restangular.one('password').one('reset').one('request').customPOST({username:username})
    }

    postPasswordReset(password, token) {
        return this.Restangular.one('password').one('reset').customPOST({reset:{plainPassword:password}, token: token})
    }
}

SecurityService.$inject = ['Restangular', 'EditableMap'];