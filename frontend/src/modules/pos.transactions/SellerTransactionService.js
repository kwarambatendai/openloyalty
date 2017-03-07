export default class SellerTransactionService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getTransactions(params) {
        return this.Restangular.all('transaction').getList(params);
    }

    postAssign(linked) {
        return this.Restangular
            .one('pos')
            .one('transaction')
            .one('customer')
            .one('assign')
            .customPOST({assign: linked})
    }

    getTransactionsByDocument(documentNumber) {
        return this.Restangular.one('seller').one('transaction').one(documentNumber).get();
    }

}

SellerTransactionService.$inject = ['Restangular', 'EditableMap'];