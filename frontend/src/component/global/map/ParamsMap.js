export default class ParamsMap {
    constructor() {
        this.config = window.OpenLoyaltyConfig;
    }

    params(data) {
        var self = this;
        var keys = Object.keys(data);
        var mapped = {
            page: data.page,
            perPage: data.count,
            sort: '',
            direction: ''
        };

        for(let k in keys) {
            if(keys[k].startsWith('sorting')) {
                mapped.sort = keys[k].replace('sorting', '').replace('[','').replace(']','');
                mapped.direction = data[keys[k]]
            }

            if(keys[k].startsWith('filter')) {
                mapped[keys[k].replace('filter', '').replace('[','').replace(']','')] = data[keys[k]]
            }
        }

        return _.pickBy(mapped)
    }
}

ParamsMap.$inject = [];