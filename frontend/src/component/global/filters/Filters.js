export default class Filters {
    static CommaToDecimal() {
        return function (value) {
            return parseFloat(value.toString().replace(',', '.'));
        }
    }

    static OLDate() {
        return function (value) {
            return moment(value).format(self.config.dateFormat);
        }
    }

    static Percent() {
        return function (value) {
            return parseInt(value * 100);
        }
    }

    static PropsFilter() {
        return function (items, props) {
            var out = [];

            if (angular.isArray(items)) {
                var keys = Object.keys(props);

                items.forEach(function (item) {
                    var itemMatches = false;

                    for (var i = 0; i < keys.length; i++) {
                        var prop = keys[i];
                        var text = props[prop].toLowerCase();
                        if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                            itemMatches = true;
                            break;
                        }
                    }

                    if (itemMatches) {
                        out.push(item);
                    }
                });
            } else {
                // Let the output be the input untouched
                out = items;
            }

            return out;
        };
    }

    static IsEmptyFilter() {
        return function(input, defaultValue) {
            if (angular.isUndefined(input) || input === null || input === '') {
                return defaultValue;
            }

            return input;
        }
    }
}