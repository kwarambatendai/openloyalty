export default class AuthService {
    constructor(Restangular, $state, jwtHelper) {
        this.Restangular = Restangular;
        this.$state = $state;
        this.jwtHelper = jwtHelper;
        this.login = null;
        this.password = null;
        this.idToken = {
            admin_: localStorage.getItem('admin_id_token') || null,
            customer_: localStorage.getItem('customer_id_token') || null,
            seller_: localStorage.getItem('seller_id_token') || null,
        };
        this.refreshToken = {
            admin_: localStorage.getItem('admin_refresh_token') || null,
            customer_: localStorage.getItem('customer_refresh_token') || null,
            seller_: localStorage.getItem('seller_refresh_token') || null
        };
        this.logoutFrom = {
            admin_: localStorage.getItem('admin_logout_from') || null,
            customer_: localStorage.getItem('customer_logout_from') || null,
            seller_: localStorage.getItem('seller_logout_from') || null
        };
        this.logoutFromParams = {
            admin_: localStorage.getItem('admin_logout_from_params') || null,
            customer_: localStorage.getItem('customer_logout_from_params') || null,
            seller_: localStorage.getItem('seller_logout_from_params') || null
        }
    }

    selectPrefix() {
        let self = this;
        let prefix = '';

        if (self.$state.includes('customer') || self.$state.includes('customer-login')) {
            prefix = 'customer_'
        }
        if (self.$state.includes('admin') || self.$state.includes('admin-login')) {
            prefix = 'admin_'
        }
        if (self.$state.includes('seller') || self.$state.includes('seller-login')) {
            prefix = 'seller_'
        }

        return prefix;
    }

    getToken() {
        let self = this;

        switch (self.selectPrefix()) {
            case 'customer_':
                return self.Restangular.one('customer').one('login_check').customPOST({
                    _username: self.login,
                    _password: self.password
                });
            case 'seller_':
                return self.Restangular.one('seller').one('login_check').customPOST({
                    _username: self.login,
                    _password: self.password
                });

            default:
                return this.Restangular.one('admin').one('login_check').customPOST({
                    _username: self.login,
                    _password: self.password
                });
        }
    }

    getRefreshToken() {
        let self = this;
        let prefix = self.selectPrefix();

        return self.Restangular.one('token').one('refresh').customPOST({
            refresh_token: self.refreshToken[prefix]
        })
    }

    getStoredToken() {
        let self = this;

        switch (self.selectPrefix()) {
            case 'customer_':
                return self.idToken['customer_'];
            case 'seller_':
                return self.idToken['seller_'];
            default:
                return self.idToken['admin_'];
        }
    }

    setStoredToken(idToken) {
        let self = this;
        let prefix = self.selectPrefix();

        localStorage.setItem(prefix + 'id_token', idToken);
        self.idToken[prefix] = idToken;
    }

    getLogoutFromParams() {
        let self = this;
        let params;

        switch (self.selectPrefix()) {
            case 'customer_':
                params = self.logoutFromParams['customer_'];
                break;
            case 'seller_':
                params = self.logoutFromParams['seller_'];
                break;
            default:
                params = self.logoutFromParams['admin_'];
                break;
        }

        try {
            params = JSON.parse(params)
        } catch (err) {
            params = {};
        }

        return params;
    }

    getLogoutFrom() {
        let self = this;
        let logoutFrom = null;
        let statesList = _.map(self.$state.get(), value => {
            return value.name
        });

        switch (self.selectPrefix()) {
            case 'customer_':
                logoutFrom = self.logoutFrom['customer_'];
                break;
            case 'seller_':
                logoutFrom = self.logoutFrom['seller_'];
                break;
            default:
                logoutFrom = self.logoutFrom['admin_'];
                break;
        }

        if (statesList.indexOf(logoutFrom) !== -1) {
            return self.logoutFrom[self.selectPrefix()];
        } else {
            return null;
        }
    }

    setLogoutFrom(stateName) {
        let self = this;
        let prefix = self.selectPrefix();

        localStorage.setItem(prefix + 'logout_from', stateName);
        self.logoutFrom[prefix] = stateName;
    }

    setLogoutFromParams(params) {
        let self = this;
        let prefix = self.selectPrefix();
        let stringParams = '{}';

        try {
            stringParams = JSON.stringify(params);
        } catch (err) {
            console.warn('Saving route params failed')
        }

        localStorage.setItem(prefix + 'logout_from_params', stringParams);
        self.logoutFromParams[prefix] = stringParams;
    }

    decodeToken(token) {
        try {
            return this.jwtHelper.decodeToken(token)
        } catch (err) {
            return null;
        }
    }

    getLoggedUserId() {
        let self = this;
        let prefix = self.selectPrefix();
        let decodedData = self.decodeToken(self.idToken[prefix]);

        if (decodedData && decodedData.id) {
            return decodedData.id;
        }

        return null;
    }

    getStoredRefreshToken() {
        let self = this;
        let prefix = self.selectPrefix();

        return self.refreshToken[prefix];
    }

    setStoredRefreshToken(refreshToken) {
        let self = this;
        let prefix = self.selectPrefix();

        localStorage.setItem(prefix + 'refresh_token', refreshToken);
        self.refreshToken[prefix] = refreshToken;
    }

    setLogin(login) {
        this.login = login;
    }

    setPassword(pass) {
        this.password = pass;
    }

    isLoggedIn() {
        let self = this;
        let prefix = self.selectPrefix();

        return self.idToken[prefix];
    }

    isRememberMe() {
        let self = this;

        if (self.$state.includes('customer') || self.$state.includes('customer-login')) {
            return self.refreshToken['customer_'];
        }
        if (self.$state.includes('admin') || self.$state.includes('admin-login')) {
            return self.refreshToken['admin_'];
        }
        if (self.$state.includes('seller') || self.$state.includes('seller-login')) {
            return self.refreshToken['seller_'];
        }

        return null;
    }

    isGranted(role) {
        let self = this;
        let index = -1;
        let prefix = self.selectPrefix();

        let decodedData = self.decodeToken(self.idToken[prefix]);
        if (decodedData && decodedData.roles) {
            index = _.findIndex(decodedData.roles, o => {
                return o === role
            });
        }

        return index !== -1;
    }

    logout() {
        let self = this;
        let prefix = self.selectPrefix();

        switch (prefix) {
            case 'customer_':
                self.$state.go('customer-login');
                break;
            case 'seller_':
                self.$state.go('seller-login');
                break;
            default:
                self.$state.go('admin-login');
                break;
        }

        self.refreshToken[prefix] = null;
        self.idToken[prefix] = null;
        localStorage.removeItem(prefix + 'id_token');
        localStorage.removeItem(prefix + 'refresh_token');
    }

    shouldLogout() {
        let self = this;

        if (self.$state.includes('admin-login') ||
            self.$state.includes('customer-login') ||
            self.$state.includes('seller-login')) {

            return null;
        } else {
            self.logout();
        }
    }
}

AuthService.$inject = ['Restangular', '$state', 'jwtHelper'];