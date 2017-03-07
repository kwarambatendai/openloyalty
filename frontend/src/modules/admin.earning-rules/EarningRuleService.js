export default class EarningRuleService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getEarningRules(params) {
        return this.Restangular.all('earningRule').getList(params);
    }

    getEarningRule(earningRule) {
        return this.Restangular.one('earningRule', earningRule).get();
    }

    postEarningRule(newEarningRule) {
        return this.Restangular.one('earningRule').customPOST({earningRule: newEarningRule});
    }

    putEarningRule(earningRuleId, editedEarningRule) {
        let self = this;
        editedEarningRule = self.Restangular.stripRestangular(editedEarningRule);

        return self.Restangular.one('earningRule', earningRuleId).customPUT({earningRule: editedEarningRule});
    }
    postActivateRule(state, ruleId) {
        let self = this;

        return this.Restangular.one('earningRule').one(ruleId).one('activate').customPOST({active: state})
    }

}

EarningRuleService.$inject = ['Restangular', 'EditableMap'];