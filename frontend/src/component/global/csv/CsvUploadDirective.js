export default class CsvUploadDirective {
    constructor() {
        this.restrict = 'A';
        this.scope = {ngModel: "=?"};
        this.replace = true;
        this.transclude = true;
        this.templateUrl = require('./templates/csv-upload.html');
        this.controller = ['$scope', '$element', ($scope, $element) => {
            $scope.fileName = '';
            $scope.chosenFile = '';
            $scope.error = '';
            $scope.showError = '';
            $scope.openFileDialog = () => {
                $element.find('input[type="file"]').trigger('click');
            };

            $scope.fileChanged = () => {
                let reader = new FileReader();
                let file = $element.find('input[type="file"]');
                $scope.file = file.get(0).files[0];

                reader.readAsText($scope.file);

                reader.onloadend = () => {
                    setTimeout(() => {
                        if (reader.result) {
                            if ($scope.file.type != 'text/csv') {
                                $scope.showError = true;
                                $scope.error = 'Only CSV files are accepted';
                                $scope.$apply()
                            } else {
                                $scope.showError = false;
                                $scope.ngModel = reader.result;
                                $scope.fileName = $scope.file.name;
                                $scope.$apply()
                            }
                        }
                    }, 1000)
                };
                $scope.fileString = reader.result;

                $scope.$watch('ngModel', () => {
                    try {
                        if(!($scope.ngModel instanceof Array))
                        $scope.ngModel = $scope.ngModel.split(';');
                    } catch (err) {
                        $scope.showError = true;
                        $scope.error = 'Invalid csv format';
                    }
                })
            }
        }]
    }
}

CsvUploadDirective.$inject = [];
