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
                    require('./templates/static/'+$stateParams.pageName+'.html');
                    return './templates/'+$stateParams.pageName+'.html';
                }
            };
        }];
    }
}

StaticPagesDirective.$inject = ['$scope', '$stateParams'];
