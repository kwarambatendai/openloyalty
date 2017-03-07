export default class CustomerTransactionService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getTransactions(params) {
        return this.Restangular.one('customer').all('transaction').getList(params);
    }
}

CustomerTransactionService.$inject = ['Restangular', 'EditableMap'];