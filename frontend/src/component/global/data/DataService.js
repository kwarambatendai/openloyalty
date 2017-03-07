export default class DataService {
    constructor(Restangular, $q, $filter) {
        this.Restangular = Restangular;
        this.$q = $q;
        this.$filter = $filter;
        this.availableLanguages = null;
        this.availableTimezones = null;
        this.availableCountries = null;
        this.availablePromotedEvents = null;
        this.availableFrontendTranslations = null;
        this._availableEarningRuleLimitPeriods = null;
        this.availableCurrencies = [
            {
                name: 'PLN',
                code: 'pln'
            },
            {
                name: 'USD',
                code: 'usd'
            },
            {
                name: 'EUR',
                code: 'eur'
            }
        ];
        this.config = window.OpenLoyaltyConfig || {};
    }

    getPointsStats() {
        return this.Restangular.one('admin').one('analytics').one('points').get()
    }

    getTransactionsStats() {
        return this.Restangular.one('admin').one('analytics').one('transactions').get()
    }

    getCustomersStats() {
        return this.Restangular.one('admin').one('analytics').one('customers').get()
    }

    getConfig() {
        return this.config;
    }

    getAvailableData() {
        let self = this;

        let languages = self.Restangular.one('settings').one('choices').one('language').get();
        let availableFrontendTranslations = self.Restangular.one('settings').one('choices').one('availableFrontendTranslations').get();
        let availableEarningRuleLimitPeriods = self.Restangular.one('settings').one('choices').one('earningRuleLimitPeriod').get();
        let timezones = self.Restangular.one('settings').one('choices').one('timezone').get();
        let countries = self.Restangular.one('settings').one('choices').one('country').get();
        let events = self.Restangular.one('settings').one('choices').one('promotedEvents').get();

        let dfd = self.$q.defer();

        self.$q.all([languages, timezones, countries, events, availableFrontendTranslations, availableEarningRuleLimitPeriods])
            .then(
                function (res) {
                    if (res[0].choices) {
                        let languages = [];
                        let index = 0;

                        for (let i in res[0].choices) {
                            languages.push({
                                _id: index,
                                name: i,
                                code: res[0].choices[i]
                            });
                            ++index;
                        }
                        self.availableLanguages = languages;
                    }

                    if (res[1].choices) {
                        let timezones = [];
                        let index = 0;

                        for (let i in res[1].choices) {
                            for (let j in res[1].choices[i]) {
                                timezones.push({
                                    _id: index,
                                    name: j,
                                    value: res[1].choices[i][j]
                                });
                                ++index;
                            }
                        }

                        self.availableTimezones = timezones;
                    }

                    if (res[2].choices) {
                        let countries = [];
                        let index = 0;

                        for (let i in res[2].choices) {
                            countries.push({
                                _id: index,
                                name: i,
                                code: res[2].choices[i]
                            });
                            ++index;
                        }

                        self.availableCountries = countries;
                    }

                    if (res[3].choices) {
                        let events = [];
                        let index = 0;

                        for (let i in res[3].choices) {
                            events.push({
                                _id: index,
                                name: i,
                                code: res[3].choices[i]
                            });
                            ++index;
                        }

                        self.availablePromotedEvents = events;
                    }
                    if (res[4].choices) {
                        let translations = [];
                        let index = 0;

                        for (let i in res[4].choices) {
                            translations.push({
                                _id: index,
                                name: res[4].choices[i].name,
                                code: res[4].choices[i].key
                            });
                            ++index;
                        }

                        self.availableFrontendTranslations = translations;
                    }
                    if (res[5].choices) {
                        let events = [];
                        let index = 0;

                        for (let i in res[5].choices) {
                            if(!res[5].choices.hasOwnProperty(i)) {
                                continue;
                            }
                            events.push({
                                _id: index,
                                name: self.$filter('translate')('earning_rule.limit.'+i),
                                code: res[5].choices[i]
                            });
                            ++index;
                        }

                        self._availableEarningRuleLimitPeriods = events;
                    }

                    dfd.resolve()
                },
                function () {
                    dfd.reject();
                }
            );

        return dfd.promise;
    }

    getDailyRegistrations() {
        return this.Restangular.one('customer').one('registrations').one('daily').get();
    }

    getCurrencies() {
        return this.availableCurrencies;
    }

    getCountries() {
        return this.availableCountries;
    }

    setCountries(data) {
        this.availableCountries = data;
    }

    getLanguages() {
        return this.availableLanguages;
    }

    setLanguages(data) {
        this.availableLanguages = data;
    }

    getTimezones() {
        return this.availableTimezones;
    }

    setTimezones(data) {
        this.availableTimezones = data;
    }

    getAvailablePromotedEvents() {
        return this.availablePromotedEvents;
    }

    setAvailablePromotedEvents(data) {
        this.availablePromotedEvents = data;
    }

    setAvailableFrontendTranslations(data) {
        this.availableFrontendTranslations = data;
    }

    getAvailableFrontendTranslations() {
        return this.availableFrontendTranslations
    }

    getAvailableEarningRuleLimitPeriods() {
        return this._availableEarningRuleLimitPeriods;
    }

    setAvailableEarningRuleLimitPeriods(value) {
        this._availableEarningRuleLimitPeriods = value;
    }
}

DataService.$inject = ['Restangular', '$q', '$filter'];