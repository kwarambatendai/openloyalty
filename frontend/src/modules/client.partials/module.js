const MODULE_NAME = 'client.partials';

angular.module(MODULE_NAME, [])

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}