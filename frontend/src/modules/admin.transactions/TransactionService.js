export default class TransactionService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getTransactions(params) {
        return this.Restangular.all('transaction').getList(params);
    }

    postTransaction(newTransaction) {
        return this.Restangular.one('transaction').customPOST({transaction: newTransaction})
    }

    postAssign(linked) {
        return this.Restangular
            .one('admin')
            .one('transaction')
            .one('customer')
            .one('assign')
            .customPOST({assign: linked})
    }

}

TransactionService.$inject = ['Restangular', 'EditableMap'];