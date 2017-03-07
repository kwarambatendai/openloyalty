export default class StaticPagesController {
    constructor($scope, $state, $stateParams) {
        this.$scope = $scope;
        this.$state = $state;
        this.$stateParams = $stateParams;
        this.adminTemplate = $state.includes('admin');
    }
}

StaticPagesController.$inject = ['$scope', '$state', '$stateParams'];