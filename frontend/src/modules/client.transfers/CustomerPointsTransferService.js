export default class CustomerPointsTransferService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getTransfers(params) {
        return this.Restangular.one('customer').one('points').all('transfer').getList(params);
    }
}

CustomerPointsTransferService.$inject = ['Restangular', 'EditableMap'];