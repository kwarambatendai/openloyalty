export default class PosService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getPosList(params) {
        return this.Restangular.all('pos').getList(params);
    }

    getPos(pos) {
        return this.Restangular.one('pos', pos).get();
    }

    postPos(newPos) {
        let self = this;

        return this.Restangular.one('pos').customPOST({pos: self.EditableMap.pos(newPos)});
    }

    putPos(posId, editedPos) {
        let self = this;

        return self.Restangular.one('pos', posId).customPUT({pos: self.Restangular.stripRestangular(self.EditableMap.pos(editedPos))});
    }

}

PosService.$inject = ['Restangular', 'EditableMap'];