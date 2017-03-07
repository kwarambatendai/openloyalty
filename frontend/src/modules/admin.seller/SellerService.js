export default class SellerService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getSellers(params) {
        return this.Restangular.all('seller').getList(params);
    }

    getSeller(sellerId) {
        return this.Restangular.one('seller', sellerId).get();
    }

    postSeller(newSeller) {
        let self = this;

        return self.Restangular.one('seller').one('register').customPOST({seller: self.EditableMap.seller(newSeller)});
    }

    putSeller(sellerId, editedSeller) {
        let self = this;

        return self.Restangular.one('seller', sellerId).customPUT({seller: self.Restangular.stripRestangular(self.EditableMap.seller(editedSeller))});
    }

    postActivateSeller(sellerId) {
        return this.Restangular.one('seller').one(sellerId).one('activate').post();
    }

    postDeactivateSeller(sellerId) {
        return this.Restangular.one('seller').one(sellerId).one('deactivate').post();
    }

    postDeleteSeller(sellerId) {
        return this.Restangular.one('seller').one(sellerId).one('delete').post();
    }

}

SellerService.$inject = ['Restangular', 'EditableMap'];