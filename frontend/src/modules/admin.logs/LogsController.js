export default class LogsController {
    constructor($scope, $state, AuthService, LogsService, DataService, Flash, NgTableParams, $q, ParamsMap, EditableMap, $filter) {
        if (!AuthService.isGranted('ROLE_ADMIN')) {
            AuthService.logout();
        }
        this.LogsService = LogsService;
        this.$scope = $scope;
        this.$state = $state;
        this.Flash = Flash;
        this.$filter = $filter;
        this.NgTableParams = NgTableParams;
        this.DataService = DataService;
        this.ParamsMap = ParamsMap;
        this.EditableMap = EditableMap;
        this.$q = $q;
        this.$filter = $filter;
        this.config = DataService.getConfig();
        this.date = {};
        this.loaderStates = {
            translationList: true,
            translationDetails: true,
            coverLoader: true
        };
    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage
        }, {
            getData: function (params) {
                let dfd = self.$q.defer();
                self.loaderStates.translationList = true;
                let parsedParams = self.ParamsMap.params(params.url());
                let from = null;
                if (self.date.from) {
                    from = new Date(self.date.from)
                }

                let to = null;
                if (self.date.to) {
                    to = new Date(self.date.to);
                }
                parsedParams.createdAtFrom = from;
                parsedParams.createdAtTo = to;
                self.LogsService.getLogsList(parsedParams)
                    .then(
                        res => {
                            self.$scope.logs = res;
                            params.total(res.total);
                            self.loaderStates.translationList = false;
                            self.loaderStates.coverLoader = false;

                            dfd.resolve(res);
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_translations.error');
                            self.Flash.create('danger', message);
                            self.loaderStates.translationList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

    getDataFromPeriod() {
        this.tableParams.reload();
    }

}

LogsController.$inject = ['$scope', '$state', 'AuthService', 'LogsService', 'DataService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', 'EditableMap', '$filter'];