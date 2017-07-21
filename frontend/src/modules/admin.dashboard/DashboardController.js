import LevelController from '../admin.levels/LevelController';

export default class DashboardController extends LevelController {
    constructor($scope, $state, AuthService, LevelService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, Validation, $filter, DataService) {
        super($scope, $state, AuthService, LevelService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, Validation, $filter, DataService);

        this.$scope.stats = {};
        this.$scope.charts = {
            registration: {
                series: [],
                data: [],
                labels: []
            }
        };
        this.DataService = DataService;

        this.loaderStates = {
            dashboardDetails: true,
            coverLoader: true
        }
    }


    _prepareChart(res, series) {
        let data = [];
        let labels = [];

        for (let day in res) {
            data.push(res[day]);
            labels.push(day)
        }

        return {
            series: series,
            data: [data],
            labels: labels,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            //suggestedMin: 0,
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                }
            }
        }
    }

    getStats() {
        let self = this;

        let promise1 = this.DataService.getPointsStats().then(res => {
            self.$scope.stats.points = res;
        });
        let promise2 = this.DataService.getTransactionsStats().then(res => {
            self.$scope.stats.transactions = res;
        });
        let promise3 = this.DataService.getCustomersStats().then(res => {
            self.$scope.stats.customers = res;
        });
        let promise4 = this.DataService.getDailyRegistrations().then(res => {
            self.$scope.dailyRegistrations = res;
            self.$scope.charts.registration = self._prepareChart(res.plain(), [self.$filter('translate')('admin.dashboard.registrations_label')])
        })

        let promise5 = this.DataService.getReferralStats().then(res => {
            self.$scope.stats.referral = res;
            console.log(self.$scope.stats.referral);
        });

        self.$q.all([promise1, promise2, promise3, promise4, promise5])
            .then(
                res => {
                    self.loaderStates.dashboardDetails = false;
                    self.loaderStates.coverLoader = false;
                }
            )
    }

}

DashboardController.$inject = ['$scope', '$state', 'AuthService', 'LevelService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', '$stateParams', 'EditableMap', 'Validation', '$filter', 'DataService'];