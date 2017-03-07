export default class Validation {
    constructor() {
    }

    skipProperty(property, data) {
        if (!property) {
            return data
        }
        if (data && data.hasOwnProperty(property)) {
            data = data[property]
        }

        for (let prop in data) {
            if (_.isEqual(data[prop], {})) {
                delete data[prop];
                continue;
            }
            if (data[prop].hasOwnProperty(property)) {
                data[prop] = this.skipProperty(property, data[prop])
            }
        }

        return data;
    }

    /**
     * @param obj
     * @param map
     * @description Validate object with defined properties. Separate asserts using '|'
     * @example let map = {name: "@assert:not_blank"}
     * @returns Object
     */
    nestedValidation(obj, map) {
        let res = {};
        let pushError = (prop, err) => {
            !(_.isObjectLike(res[prop])) ? res[prop] = {} : '';
            !(res[prop].errors instanceof Array) ? res[prop].errors = [] : '';
            res[prop].errors.push(err);
        };
        let emptyField = prop => {
            return !(obj.hasOwnProperty(prop) && (!_.isEmpty(obj[prop]) || (typeof obj[prop] === 'boolean' && obj[prop] !== false) || typeof obj[prop] === 'number'))
        };

        !_.isObjectLike(obj) ? obj = {} : '';

        for (let prop in map) {
            if (_.isObjectLike(map[prop])) {
                if (obj.hasOwnProperty(prop) && _.isObjectLike(obj[prop])) {
                    res[prop] = this.nestedValidation(obj[prop], map[prop])
                } else {
                    res[prop] = this.nestedValidation({}, map[prop])
                }
            } else if (typeof map[prop] === 'string') {
                let asserts = map[prop].replace('@assert:', '').split('|');

                for (let i in asserts) {
                    if (asserts[i] === 'not_blank') {
                        if (emptyField(prop)) {
                            pushError(prop, 'front_error.not_blank');
                        }
                    }
                    if (asserts[i].match(/^equal_with/)) {
                        let arr = asserts[i].split(':');
                        let equalWith = arr.length === 2 ? arr[1] : false;

                        if (equalWith && obj.hasOwnProperty(equalWith)) {
                            if (obj[equalWith] !== obj[prop]) {
                                pushError(prop, 'front_error.not_equal_' + equalWith);
                            }
                        }
                    }
                    if (asserts[i].match(/^or_field/)) {
                        let arr = asserts[i].split(':');
                        let orField = arr.length === 2 ? arr[1] : false;

                        if (emptyField(prop) && emptyField(orField)) {
                            pushError(prop, 'front_error.not_blank_or_' + orField);
                        }

                    }
                    if (asserts[i].match(/^one_from/)) {
                        let arr = asserts[i].split(':');
                        let err = true;

                        for (let j = 1; j < arr.length; j++) {
                            let orField = arr[j];
                            if (!(emptyField(prop) && emptyField(orField))) {
                                err = false;
                                break;
                            }
                        }
                        if(err) {
                            pushError(prop, 'front_error.at_least_one');
                        }
                    }
                }
            } else {
                console.warn('Wrong front validation definition');
            }
        }

        return res;
    }

    mapSymfonyValidation(formResponse) {
        if (formResponse && !formResponse.hasOwnProperty('form')) {
            return true
        }
        let form = formResponse.form;

        return this.skipProperty('children', form);
    }

    frontValidation(obj, validationMap) {
        let first = angular.copy(obj);
        let second = angular.copy(validationMap);
        let valid = this.nestedValidation(first, second);

        return _.omitBy(valid, _.isEmpty);
    }
}

Validation.$inject = [];