export default class LevelService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getLevels(params) {
        return this.Restangular.all('level').getList(params);
    }

    getLevel(levelId) {
        return this.Restangular.one('level', levelId).get();
    }

    postLevel(newLevel) {
        return this.Restangular.one('level').one('create').customPOST({level: newLevel})
    }

    getLevelCustomers(params, levelId) {
        if(!params) {
            params = {}
        }
        return this.Restangular.one('level', levelId).all('customers').getList();
    }
    getFile(levelId) {
        return this.Restangular.setFullResponse(true).one('csv').one('level', levelId).get();
    }
    putLevel(editedLevel) {
        let self = this;

        return editedLevel.customPUT({level: self.EditableMap.level(editedLevel)});
    }

    postActivateLevel(state, levelId) {
        let self = this;

        return this.Restangular.one('level').one(levelId).one('activate').customPOST({active: state})
    }
}

LevelService.$inject = ['Restangular', 'EditableMap'];