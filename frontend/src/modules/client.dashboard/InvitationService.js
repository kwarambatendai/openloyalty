export default class InvitationService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    invite(invitation) {
        return this.Restangular.one('invitations').one('invite').customPOST({invitation: invitation})
    }

}

InvitationService.$inject = ['Restangular', 'EditableMap'];