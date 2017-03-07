export default class TranslationService {
    constructor(Restangular, $q, $rootScope, $timeout) {
        this.Restangular = Restangular;
        this.$q = $q;
        this.$rootScope = $rootScope;
        this.$timeout = $timeout;

        this.translations = null;

        this._init();
    }

    _init() {
        let self = this;
        let storedTranslations = localStorage.getItem('oloy_translations') || null;

        if (storedTranslations && storedTranslations !== '') {
            try {
                self.translations = JSON.parse(storedTranslations)
            } catch (err) {
                console.warn('Can\'t read stored translations')
            }
        }
    }

    getTranslations() {
        let self = this;
        let dfd = self.$q.defer();

        self.$rootScope.pendingRequests = _.isNumber(self.$rootScope.pendingRequests) ? self.$rootScope.pendingRequests : 0;
        self.$rootScope.pendingRequests += 1;

        if (self.translations) {
            if (self.translations.global) {
                self.translations.global.contentLoadedTest = 'ok';
            } else {
                self.translations.global = {};
                self.translations.global.contentLoadedTest = 'ok';
            }
            self.$timeout(()=> {
                self.$rootScope.pendingRequests -= 1;
            }, 1500);
            dfd.resolve(self.translations)
        } else {
            self.Restangular.one('translations').get()
                .then(
                    res => {
                        self.translations = res.plain();
                        if (self.translations.global) {
                            self.translations.global.contentLoadedTest = 'ok';
                        } else {
                            self.translations.global = {};
                            self.translations.global.contentLoadedTest = 'ok';
                        }
                        self.storeTranslations(self.translations);
                        self.$timeout(()=> {
                            self.$rootScope.pendingRequests -= 1;
                        }, 1500);
                        dfd.resolve(self.translations)
                    }
                )
        }

        return dfd.promise
    }

    storeTranslations(translations) {
        translations = JSON.stringify(translations);

        localStorage.setItem('oloy_translations', translations);
    }

    removeStoredTranslations() {
        localStorage.setItem('oloy_translations', '');
    }

}

TranslationService.$inject = ['Restangular', '$q', '$rootScope', '$timeout'];