export default class DatepickerDirective {
    constructor() {
        this.config = window.OpenLoyaltyConfig;
        this.restrict = 'A';
        this.link = function (scope, element) {

            if (element.attr('no-time')) {

                element.prop('placeholder', this.config.dateFormat);
                element.datetimepicker(
                    {
                        timepicker: false,
                        format: 'Y-m-d',
                        yearStart: 1900
                    }
                );
            } else {

                element.prop('placeholder', this.config.dateTimeFormat);
                element.datetimepicker(
                    {
                        timepicker: true,
                        format: 'Y-m-d H:i',
                        yearStart: 1900
                    }
                );
            }
        }
    }
}

DatepickerDirective.$inject = [];