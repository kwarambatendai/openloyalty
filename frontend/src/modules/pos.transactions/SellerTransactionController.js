export default class SellerTransactionController {
    constructor($scope, $state, AuthService, SellerTransactionService, Flash, ParamsMap, $stateParams, EditableMap, $filter, Validation) {
        if (!AuthService.isGranted('ROLE_SELLER')) {
            AuthService.logout();
        }
        this.$scope = $scope;
        this.$scope.linked = {};
        this.SellerTransactionService = SellerTransactionService;
        this.$state = $state;
        this.Flash = Flash;
        this.ParamsMap = ParamsMap;
        this.EditableMap = EditableMap;
        this.$filter = $filter;
        this.validateFields = {
            customerLoyaltyCardNumber: '@assert:or_field:customerPhoneNumber',
            customerPhoneNumber: '@assert:or_field:customerLoyaltyCardNumber',
            transactionDocumentNumber: '@assert:not_blank'
        };
        this.Validation = Validation;
        this.transactionConfig = {
            valueField: 'documentNumber',
            labelField: 'documentNumber',
            create: false,
            sortField: 'documentNumber',
            searchField: 'documentNumber',
            maxItems: 1,
        };
        this.customersConfig = {
            valueField: 'id',
            labelField: 'id',
            create: false,
            sortField: 'id',
            searchField: 'id',
            maxItems: 1,
        };
        this.$scope.search = {};
        this.validateFindTransaction = {
            documentNumber: '@assert:or_field:documentNumber',
        };
        SellerTransactionService.getTransactions()
            .then(
                res => {
                    this.transactionsToLink = res;
                }
            )
    }

    linkTransaction() {
        let self = this;
        self.$scope.validate = {};
        let frontValidation = self.Validation.frontValidation(self.$scope.linked, self.validateFields);

        if (_.isEmpty(frontValidation)) {
            if (self.$scope.linked) {
                self.SellerTransactionService.postAssign(self.$scope.linked)
                    .then(
                        () => {
                            self.$state.go('seller.panel.dashboard');
                            let message = self.$filter('translate')('xhr.post_transaction_assign.success');
                            self.Flash.create('success', message);
                        },
                        res => {
                            self.$scope.validate = self.Validation.mapSymfonyValidation(res.data);
                            let message = self.$filter('translate')('xhr.post_transaction_assign.error');
                            self.Flash.create('danger', message);
                        }
                    );
            }
        } else {
            let message = self.$filter('translate')('xhr.post_transaction_assign.error');
            self.Flash.create('danger', message);
            self.$scope.validate = frontValidation;
        }
    }

    search() {
        let self = this;
        let frontValidation = self.Validation.frontValidation(self.$scope.search, self.validateFindTransaction);

        if (_.isEmpty(frontValidation)) {
            self.SellerTransactionService.getTransactionsByDocument(self.$scope.search.documentNumber).then(
                res => {
                    self.$scope.transactions = res.transactions;
                    self.$scope.validate = {};
                },
                res => {
                    let message = self.$filter('translate')('xhr.get_transaction_search.error');
                    self.Flash.create('danger', message);
                }
            );
        }
        else {
            let message = self.$filter('translate')('xhr.get_transaction_search.error');
            self.Flash.create('danger', message);
            self.$scope.validate = frontValidation;
        }
    }
}

SellerTransactionController.$inject = ['$scope', '$state', 'AuthService', 'SellerTransactionService', 'Flash', 'ParamsMap', '$stateParams', 'EditableMap', '$filter', 'Validation'];