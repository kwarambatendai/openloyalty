export default class DebugController {
    constructor($scope, Flash, $q, $filter, TranslationService) {
        this.$scope = $scope;
        this.Flash = Flash;
        this.$q = $q;
        this.$filter = $filter;
        this.TranslationService = TranslationService;
    }

    loadTranslations() {
        let self = this;
        let trans;

        if (!this.$scope.translationLoader) {
            let message = self.$filter('translate')('Empty translations');
            this.Flash.create('danger', message);
            return;
        }

        try {
            trans = JSON.parse(this.$scope.translationLoader)
        } catch (err) {
            let message = self.$filter('translate')('Invalid translation format (JSON required)');
            this.Flash.create('danger', message);
            return;
        }

        try {
            this.TranslationService.storeTranslations(trans);
            let message = self.$filter('translate')('Translation loaded. Refresh your app to load');
            this.Flash.create('success', message);
        } catch (err) {
            let message = self.$filter('translate')('Invalid translation format (JSON required)');
            this.Flash.create('danger', message);
        }
    }

    clear() {
        this.TranslationService.removeStoredTranslations();
        let message = this.$filter('translate')('Translation deleted, refresh app to call for backend translations');
        this.Flash.create('success', message);
    }

}

DebugController.$inject = ['$scope', 'Flash', '$q', '$filter', 'TranslationService'];