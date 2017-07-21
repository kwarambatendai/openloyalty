export default class LogsService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getLogsList(params) {
        return this.Restangular.one('audit').all('log').getList(params);
    }
}

LogsService.$inject = ['Restangular', 'EditableMap'];