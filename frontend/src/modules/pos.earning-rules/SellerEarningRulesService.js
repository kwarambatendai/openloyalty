export default class SellerEarningRulesService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getEarningRules(params) {
        return this.Restangular.one('seller').all('earningRule').getList(params);
    }

}

SellerEarningRulesService.$inject = ['Restangular', 'EditableMap'];