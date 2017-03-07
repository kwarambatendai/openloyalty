export default class CustomerStatusService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getStatus($id) {
        return this.Restangular.one('customer').one($id).one('status').get();
    }
}

CustomerStatusService.$inject = ['Restangular', 'EditableMap'];