export default class SegmentController {
    constructor($scope, $state, AuthService, SegmentService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, DataService, PosService, Validation, $filter) {
        if (!AuthService.isGranted('ROLE_ADMIN')) {
            AuthService.logout();
        }
        this.$scope = $scope;
        this.SegmentService = SegmentService;
        this.$state = $state;
        this.Flash = Flash;
        this.$scope.newSegment = {parts: [{criteria: []}]};
        this.$scope.editableFields = {};
        this.$scope.validate = {};
        this.segmentId = $stateParams.segmentId || null;
        this.$scope.segmentName = $stateParams.segmentName || false;
        this.NgTableParams = NgTableParams;
        this.ParamsMap = ParamsMap;
        this.EditableMap = EditableMap;
        this.config = DataService.getConfig();
        this.$q = $q;
        this.$scope.removeCriterion = this.removeCriterion;
        this.Validation = Validation;
        this.$filter = $filter;
        this.active = [
            {
                name: this.$filter('translate')('global.active'),
                type: 1
            },
            {
                name: this.$filter('translate')('global.inactive'),
                type: 0
            }
        ];
        this.activeConfig = {
            valueField: 'type',
            labelField: 'name',
            create: false,
            sortField: 'name',
            maxItems: 1
        };
        this.posPromise = PosService.getPosList({})
            .then(
                res => {
                    $scope.pos = res;
                },
                () => {

                }
            );

        this.loaderStates = {
            segmentList: true,
            userList: true,
            segmentDetails: true,
            coverLoader: true,
            removeSegment: false
        }
    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage,
            sorting: {
                createdAt: 'desc'
            }
        }, {
            getData: function (params) {
                let dfd = self.$q.defer();
                self.loaderStates.segmentList = true;

                self.SegmentService.getSegments(self.ParamsMap.params(params.url()))
                    .then(
                        function (res) {
                            self.$scope.segments = res;
                            params.total(res.total);
                            self.loaderStates.segmentList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.resolve(res);
                        },
                        function () {
                            let message = self.$filter('translate')('xhr.get_segments.error');
                            self.Flash.create('danger', message);
                            self.loaderStates.segmentList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

    getSegmentData() {
        let self = this;
        self.loaderStates.segmentDetails = true;

        if (self.segmentId) {
            self.SegmentService.getSegment(self.segmentId)
                .then(
                    res => {
                        self.$scope.segment = res;
                        self.$scope.editableFields = self.EditableMap.humanizeSegment(res);
                        self.loaderStates.segmentDetails = false;
                        self.loaderStates.coverLoader = false;
                    },
                    () => {
                        let message = self.$filter('translate')('xhr.get_segment.error');
                        self.Flash.create('danger', message);
                        self.loaderStates.segmentDetails = false;
                        self.loaderStates.coverLoader = false;
                    }
                )
        } else {
            self.$state.go('admin.segment-list');
            let message = self.$filter('translate')('xhr.get_segments.no_id');
            self.Flash.create('warning', message);
            self.loaderStates.segmentDetails = false;
        }
    }

    getSegmentCustomersData() {
        let self = this;

        if (self.segmentId) {
            self.tableCustomerParams = new self.NgTableParams({
                count: self.config.perPage
            }, {
                getData: function (params) {
                    let dfd = self.$q.defer();
                    self.loaderStates.userList = true;

                    self.SegmentService.getSegmentCustomers(self.ParamsMap.params(params.url()), self.segmentId)
                        .then(
                            function (res) {
                                self.$scope.customers = res;
                                params.total(res.total);
                                self.loaderStates.userList = false;
                                self.loaderStates.coverLoader = false;
                                dfd.resolve(res);
                            },
                            function () {
                                let message = self.$filter('translate')('xhr.get_segment_customers.error');
                                self.Flash.create('danger', message);
                                self.loaderStates.userList = false;
                                self.loaderStates.coverLoader = false;
                                dfd.reject();
                            }
                        );

                    return dfd.promise;
                }
            });
        } else {
            self.$state.go('admin.segment-list');
            let message = self.$filter('translate')('xhr.get_segment_customers.no_id');
            self.Flash.create('warning', message);
            self.loaderStates.userList = false;
        }
    }

    getSegmentCsvData(segmentId, segmentName) {
        let self = this;
        if (segmentId) {
            self.SegmentService.getFile(segmentId)
                .then(
                    function (res) {
                        var date = new Date();
                        var blob = new Blob([res.data], {type: res.headers('Content-Type')});
                        var downloadLink = angular.element('<a></a>');
                        downloadLink.attr('href', window.URL.createObjectURL(blob));
                        downloadLink.attr('download', segmentName.replace(" ", "-") + "-" + date.toISOString().substring(0, 10) + ".csv");
                        downloadLink[0].click();
                    }
                );
        }
    }

    editSegment(editedSegment) {
        let self = this;

        self.SegmentService.putSegment(self.segmentId, editedSegment)
            .then(
                res => {
                    let message = self.$filter('translate')('xhr.put_segment.success');
                    self.Flash.create('success', message);
                    self.$state.go('admin.segment-list')
                },
                res => {
                    self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                    let message = self.$filter('translate')('xhr.put_segment.error');
                    self.Flash.create('danger', message);
                }
            )
    }

    addSegment(newSegment) {
        let self = this;

        self.SegmentService.postSegment(newSegment)
            .then(
                res => {
                    let message = self.$filter('translate')('xhr.post_segment.success');
                    self.Flash.create('success', message);
                    self.$state.go('admin.segment-list')
                },
                res => {
                    self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                    let message = self.$filter('translate')('xhr.post_segment.error');
                    self.Flash.create('danger', message);
                }
            );
    }

    setSegmentState(state, segmentId) {
        let self = this;

        if (state) {
            self.SegmentService.postActivateSegment(segmentId)
                .then(
                    () => {
                        let message = self.$filter('translate')('xhr.post_activate_segment.success');
                        self.Flash.create('success', message);
                        self.tableParams.reload();
                    },
                    () => {
                        let message = self.$filter('translate')('xhr.post_activate_segment.error');
                        self.Flash.create('danger', message);
                    }
                )
        } else {
            self.SegmentService.postDeactivateSegment(segmentId)
                .then(
                    () => {
                        let message = self.$filter('translate')('xhr.post_deactivate_segment.success');
                        self.Flash.create('success', message);
                        self.tableParams.reload();
                    },
                    () => {
                        let message = self.$filter('translate')('xhr.post_deactivate_segment.error');
                        self.Flash.create('danger', message);
                    }
                )
        }
    }

    removeSegment(segmentId) {
        let self = this;
        self.loaderStates.removeSegment = true;

        self.SegmentService.deleteSegment(segmentId)
            .then(
                () => {
                    let message = self.$filter('translate')('xhr.delete_segment.success');
                    self.Flash.create('success', message);
                    self.tableParams.reload();
                    self.loaderStates.removeSegment = false;
                },
                () => {
                    let message = self.$filter('translate')('xhr.delete_segment.error');
                    self.Flash.create('danger', message);
                    self.loaderStates.removeSegment = false;
                }
            )
    }

    removeSegmentPart(index, edit) {
        let self = this;
        let segment;

        if (!edit) {
            segment = self.$scope.newSegment;
        } else {
            segment = self.$scope.editableFields;
        }

        segment.parts = _.difference(segment.parts, [segment.parts[index]])
    }

    addSegmentPart(edit) {
        let self = this;
        let segment;

        if (!edit) {
            segment = self.$scope.newSegment;
        } else {
            segment = self.$scope.editableFields;
        }

        segment.parts.push({criteria: []})
    }

    removeCriterion(index, parent, edit) {
        let self = this;
        let segment;

        if (!edit) {
            segment = self.$scope.newSegment
        } else {
            segment = self.$scope.editableFields
        }

        segment.parts[parent].criteria = _.difference(segment.parts[parent].criteria, [segment.parts[parent].criteria[index]]);
    }

    addCriterion(parent, edit) {
        let self = this;
        let segment;

        if (!edit) {
            segment = self.$scope.newSegment;
        } else {
            segment = self.$scope.editableFields;
        }

        segment.parts[parent].criteria.push({})
    }
}

SegmentController.$inject = ['$scope', '$state', 'AuthService', 'SegmentService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', '$stateParams', 'EditableMap', 'DataService', 'PosService', 'Validation', '$filter'];