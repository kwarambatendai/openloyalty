export default class TranslationsService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getTranslationsList(params) {
        return this.Restangular.one('admin').all('translations').getList(params);
    }
    getTranslation(id) {
        return this.Restangular.one('admin').one('translations').one(id).get();
    }

    putTranslation(translationId, edited) {
        let self = this;
        edited = self.Restangular.stripRestangular(edited);

        return self.Restangular.one('admin').one('translations', translationId).customPUT({translation: edited});
    }
    postTranslation(translation) {
        let self = this;

        return self.Restangular.one('admin').one('translations').customPOST({translation: translation});
    }

}

TranslationsService.$inject = ['Restangular', 'EditableMap'];