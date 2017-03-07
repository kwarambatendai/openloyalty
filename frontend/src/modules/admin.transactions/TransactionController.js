const DOCUMENT_TYPE_RETURN = 'return';
const DOCUMENT_TYPE_SELL = 'sell';

export default class TransactionController {
    constructor($scope, $state, AuthService, TransactionService, Flash, NgTableParams, $q, ParamsMap, $stateParams, EditableMap, Validation, $filter, CustomerService, DataService) {
        if (!AuthService.isGranted('ROLE_ADMIN')) {
            AuthService.logout();
        }
        this.TransactionService = TransactionService;
        this.CustomerService = CustomerService;
        this.Validation = Validation;
        this.ParamsMap = ParamsMap;
        this.EditableMap = EditableMap;
        this.config = window.OpenLoyaltyConfig;

        this.Flash = Flash;
        this.NgTableParams = NgTableParams;
        this.$q = $q;
        this.$filter = $filter;

        this.$state = $state;
        this.$scope = $scope;

        this._init();
        this._selectizeConfigs();

        this.loaderStates = {
            transactionList: true,
            coverLoader: true,
            addTransaction: false
        }
    }

    getData() {
        let self = this;

        self.tableParams = new self.NgTableParams({
            count: self.config.perPage,
            sorting: {
                purchaseDate: 'desc'
            }
        }, {
            getData: function(params) {
                let dfd = self.$q.defer();
                self.loaderStates.transactionList = true;

                self.TransactionService.getTransactions(self.ParamsMap.params(params.url()))
                    .then(
                        res => {
                            self.$scope.transactions = res;
                            params.total(res.total);
                            self.loaderStates.transactionList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.resolve(res);
                        },
                        () => {
                            let message = self.$filter('translate')('xhr.get_transations.error');
                            self.Flash.create('danger', message);
                            self.loaderStates.transferList = false;
                            self.loaderStates.coverLoader = false;
                            dfd.reject();
                        }
                    );

                return dfd.promise;
            }
        });
    }

    linkTransaction(linked) {
        let self = this;
        self.loaderStates.addTransaction = true; 

        if (self.$scope.linked) {
            self.TransactionService.postAssign(self.$scope.linked)
                .then(
                    () => {
                        let message = self.$filter('translate')('xhr.post_transaction_assign.success');
                        self.Flash.create('success', message);
                        self.$scope.linkTransactionModal = false;
                        self.loaderStates.addTransaction = false; 
                    },
                    res => {
                        let message = self.$filter('translate')('xhr.post_transaction_assign.error');
                        self.Flash.create('danger', message);
                        self.$scope.linkedValidate = self.Validation.mapSymfonyValidation(res.data);
                        self.loaderStates.addTransaction = false; 
                    }
                );
        }
    }

    _selectizeConfigs() {
        let self = this;

        this.documentTypeSelectOptions = [{
                value: '',
                label: self.$filter('translate')('transaction.document_types.both')
            },
            {
                value: DOCUMENT_TYPE_RETURN,
                label: self.$filter('translate')('transaction.document_types.' + DOCUMENT_TYPE_RETURN)
            },
            {
                value: DOCUMENT_TYPE_SELL,
                label: self.$filter('translate')('transaction.document_types.' + DOCUMENT_TYPE_SELL)
            }
        ];

        this.documentTypeSelectConfig = {
            valueField: 'value',
            labelField: 'label',
            create: false,
            sortField: 'label',
            searchField: 'label',
            maxItems: 1,
            allowEmptyOption: true
        };

        this.transactionConfig = {
            valueField: 'documentNumber',
            labelField: 'documentNumber',
            create: false,
            sortField: 'documentNumber',
            searchField: 'documentNumber',
            maxItems: 1,
        };

        this.customersConfig = {
            valueField: 'customerId',
            labelField: 'email',
            create: false,
            sortField: 'email',
            maxItems: 1,
            searchField: 'email',
            placeholder: this.$filter('translate')('global.start_typing_an_email'),
            onChange: () => {
                self.$scope.clientSearch = 0;
            },
            load: (query, callback) => {
                if (!query.length) return callback();

                self.$scope.clientSearch = 1;

                self.CustomerService.getCustomers(self.ParamsMap.params({
                        'filter[email]': query,
                        'filter[silenceQuery]': true
                    }))
                    .then(
                        res => {
                            self.$scope.clientSearch = 0;
                            callback(res)
                        },
                        () => {
                            callback();
                        }
                    );

            }
        };

        this.transactionConfig = {
            valueField: 'documentNumber',
            labelField: 'documentNumber',
            create: false,
            sortField: 'documentNumber',
            maxItems: 1,
            searchField: 'documentNumber',
            placeholder: this.$filter('translate')('global.start_typing_a_number'),
            onChange: value => {
                self.$scope.documentSearch = 0;
            },
            load: (query, callback) => {
                if (!query.length) return callback();

                self.$scope.documentSearch = 1;

                self.TransactionService.getTransactions(self.ParamsMap.params({
                        'filter[documentNumber]': query,
                        'filter[silenceQuery]': true
                    }))
                    .then(
                        res => {
                            self.$scope.documentSearch = 0;
                            callback(res)
                        },
                        () => {
                            callback();
                        }
                    );

            }
        };
    }

    _init() {
        this.$scope.linked = {};
        this.$scope.clientSearch = 0; //0 - nothing, 1 - loading, 2 - nothing found
        this.$scope.documentSearch = 0; //0 - nothing, 1 - loading, 2 - nothing found
    }
}

TransactionController.$inject = ['$scope', '$state', 'AuthService', 'TransactionService', 'Flash', 'NgTableParams', '$q', 'ParamsMap', '$stateParams', 'EditableMap', 'Validation', '$filter', 'CustomerService', 'DataService'];