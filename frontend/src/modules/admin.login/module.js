import LoginController from './LoginController';

const MODULE_NAME = 'admin.login';

angular.module(MODULE_NAME, [])
    .controller('LoginController', LoginController);

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
