export default class StaticPagesDirective {
    constructor() {
        this.restrict = 'E';
        this.scope = {
            page: '='
        };
        this.template = '<ng-include src="getTemplateUrl()"/>';
        this.controller = ['$scope', '$stateParams', ($scope, $stateParams) => {
            $scope.getTemplateUrl = () => {
                if ($stateParams.pageName) {
                    return './templates/static/'+$stateParams.pageName+'.html';
                }
            };
        }];
    }
}

StaticPagesDirective.$inject = ['$scope', '$stateParams'];