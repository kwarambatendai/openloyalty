export default class CustomerEarningRulesService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getRules($id) {
        return this.Restangular.one('customer').one('earningRule').get();
    }
}

CustomerEarningRulesService.$inject = ['Restangular', 'EditableMap'];