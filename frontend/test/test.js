describe("first test", function () {
    it("should add 2 and 2", function () {
        expect(2 + 2).toBe(4);
    });
});

describe('Module "app"', function () {
    var module;
    var moduleDependencies;

    beforeEach(function () {
        module = angular.module('OpenLoyalty');
        moduleDependencies = module.requires;
    });

    it('should be registered', function () {
        expect(module).toBeDefined();
    });

    it('should have "ui.router" as a dependency', function () {
        expect(moduleDependencies).toContain('ui.router');
    });

    it('should have "angular-jwt" as a dependency', function () {
        expect(moduleDependencies).toContain('angular-jwt');
    });

    it('should have "restangular" as a dependency', function () {
        expect(moduleDependencies).toContain('restangular');
    });

    it('should have "ngFlash" as a dependency', function () {
        expect(moduleDependencies).toContain('ngFlash');
    });

    it('should have "angularMoment" as a dependency', function () {
        expect(moduleDependencies).toContain('angularMoment');
    });

    it('should have "ngTable" as a dependency', function () {
        expect(moduleDependencies).toContain('ngTable');
    });

    it('should have "ui.select" as a dependency', function () {
        expect(moduleDependencies).toContain('ui.select');
    });

    it('should have "ngSanitize" as a dependency', function () {
        expect(moduleDependencies).toContain('ngSanitize');
    });

    it('should have "ng-sortable" as a dependency', function () {
        expect(moduleDependencies).toContain('ng-sortable');
    });

    it('should have "pascalprecht.translate" as a dependency', function () {
        expect(moduleDependencies).toContain('pascalprecht.translate');
    });

    it('should have "selectize" as a dependency', function () {
        expect(moduleDependencies).toContain('selectize');
    });

});


describe('RootController', function () {
    var ctrl;

    beforeEach(module('OpenLoyalty'));
    beforeEach(inject(function ($controller, _$rootScope_) {
        var $scope = _$rootScope_.$new();
        ctrl = $controller('RootController', {
            $scope: $scope
        });
    }));

    it('should be defined', function () {
        expect(ctrl).toBeDefined();
    });

    it('should check if state is login', function () {
        ctrl.$state = {
            current: {
                name: 'admin-login'
            }
        };
        expect(ctrl.loggedIn()).toBe(false);
        ctrl.$state.current.name = 'customer-login';
        expect(ctrl.loggedIn()).toBe(false);
        ctrl.$state.current.name = 'any-other-state';
        expect(ctrl.loggedIn()).toBe(true);
    });

    it('should set loading', function () {
        ctrl.setLoading(true);
        expect(ctrl.loading).toBe(true);
    });

    it('should have logout method', function () {
        expect(ctrl.logout).toBeDefined();
    });
});


describe('CustomerController', function () {
    var ctrl;
    var $scope;
    var customers = ['customer1', 'customer2'];
    var $httpBackend;
    var utils;

    beforeEach(module('OpenLoyalty'));
    beforeEach(module('testUtils'));
    beforeEach(inject(function ($controller, _$rootScope_, _utils_, $injector) {
        $scope = _$rootScope_.$new();
        utils = _utils_;

        $httpBackend = $injector.get('$httpBackend');
        $httpBackend.whenGET('./templates/login.html').respond(200, '');

        ctrl = $controller('CustomerController', {
            $scope: $scope,
        });
        ctrl.CustomerService = {
            getCustomers: () => {
                return {cutomers, total: 2}
            }
        }

    }));

    it('newCustomer should be empty object', function () {
        expect(ctrl.$scope.newCustomer).toEqual({});
    });

    it('showCompany should be false', function () {
        expect(ctrl.$scope.showCompany).toBe(false);
    });

    it('showAddress should be false', function () {
        expect(ctrl.$scope.showAddress).toBe(false);
    });

    it('showAddress should be false', function () {
        expect(ctrl.customerId).toBe(null);
    });

    it('should call for customers list', function () {
        ctrl.getData();
        expect($scope.customers).not.toBe(undefined);
        expect($scope.error).toBe(undefined);
    });


});