export default class TransferService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getTransfers(params) {
        return this.Restangular.one('points').all('transfer').getList(params);
    }

    postAddTransfer(newTransfer) {
        return this.Restangular.one('points').one('transfer').one('add').customPOST({transfer:newTransfer});
    }

    postSpendTransfer(spendTransfer) {
        return this.Restangular.one('points').one('transfer').one('spend').customPOST({transfer: spendTransfer});
    }

    postCancelTransfer(transferId) {
        return this.Restangular.one('points').one('transfer', transferId).one('cancel').post();
    }

}

TransferService.$inject = ['Restangular', 'EditableMap'];