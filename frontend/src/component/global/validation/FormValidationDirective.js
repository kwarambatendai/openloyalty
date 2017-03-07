export default class FromValidationDirective {
    constructor() {
        this.restrict = 'A';
        this.scope = {formValidation: "=?"};
        this.replace = true;
        this.transclude = true;
        this.templateUrl = './templates/formFieldError.html';
    }
}

FromValidationDirective.$inject = [];