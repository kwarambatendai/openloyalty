export default class SettingsService {
    constructor(Restangular, $q, EditableMap) {
        this.Restangular = Restangular;
        this.$q = $q;
        this.EditableMap = EditableMap;
        this.settings = null;
    }

    getSettingsData() {
        let self = this;
        let dfd = self.$q.defer();

        self.Restangular.one('settings').get()
            .then(
                 res => {
                    self.settings = self._toObject(res.settings);
                    dfd.resolve();
                },
                () => {
                    dfd.reject();
                }
            );

        return dfd.promise;
    }

    postSettings(editedSettings) {
        let self = this;
        let data = self.EditableMap.settings(editedSettings);

        return self.Restangular.one('settings').customPOST({settings: data});
    }

    getSettings() {
        return this.settings;
    }

    _toObject(data) {
        let res = {};
        for(let i in data) {
            res[i] = data[i]
        }

        return res;
    }

}

SettingsService.$inject = ['Restangular', '$q', 'EditableMap'];