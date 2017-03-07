export default class SegmentPartDirective {
    constructor() {
        this.restrict = 'E';
        this.scope = {
            segmentIndex: "=",
            parentIndex: "=",
            selectedType: '=',
            segmentModel: '=',
            edit: '=',
            validate: '='

        };
        this.templateUrl = './templates/partials/segment-part.html';
        this.replace = true;
        this.controller = ['$scope', '$filter', ($scope, $filter) => {
            $scope.types = [
                {
                    code: 'bought_in_pos',
                    name: $filter('translate')('segment.partials.bought_in_pos')
                },
                {
                    code: 'transaction_count',
                    name: $filter('translate')('segment.partials.transaction_count')
                },
                {
                    code: 'average_transaction_amount',
                    name: $filter('translate')('segment.partials.average_transaction_amount')
                },
                {
                    code: 'transaction_percent_in_pos',
                    name: $filter('translate')('segment.partials.transaction_percent_in_pos')
                },
                {
                    code: 'purchase_period',
                    name: $filter('translate')('segment.partials.purchase_period')
                },
                {
                    code: 'bought_labels',
                    name: $filter('translate')('segment.partials.bought_labels')
                },
                {
                    code: 'bought_makers',
                    name: $filter('translate')('segment.partials.bought_makers')
                },
                {
                    code: 'anniversary',
                    name: $filter('translate')('segment.partials.anniversary')
                },
                {
                    code: 'last_purchase_n_days_before',
                    name: $filter('translate')('segment.partials.last_purchase_n_days_before')
                },
                {
                    code: 'bought_skus',
                    name: $filter('translate')('segment.partials.bought_skus')
                },
                {
                    code: 'transaction_amount',
                    name: $filter('translate')('segment.partials.transaction_amount')
                },

            ];
            $scope.segmentTypeSelect = {
                valueField: 'code',
                labelField: 'name',
                create: false,
                sortField: 'name',
                maxItems: 1,
            };

            $scope.getTemplateUrl = () => {
                if ($scope.segmentModel && $scope.segmentModel.type) {
                    return './templates/partials/' + $scope.segmentModel.type + '.html'
                } else {
                    return ''
                }
            };
            $scope.addCriterion = parent => {
                let segment;

                if (!$scope.edit) {
                    segment = $scope.$parent.$parent.newSegment;
                } else {
                    segment = $scope.$parent.$parent.editableFields;
                }

                segment.parts[parent].criteria.push({})
            };
            $scope.removeCriterion = (index, parent) => {
                let segment;

                if (!$scope.edit) {
                    segment = $scope.$parent.$parent.newSegment
                } else {
                    segment = $scope.$parent.$parent.editableFields
                }

                segment.parts[parent].criteria = _.difference(segment.parts[parent].criteria, [segment.parts[parent].criteria[index]]);
            }
        }];
    }
}