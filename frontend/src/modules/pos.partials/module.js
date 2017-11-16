const MODULE_NAME = 'pos.partials';

angular.module(MODULE_NAME, [])
    .run(($templateCache) => {
        $templateCache.put('templates/pos-top-nav.html', require('./templates/pos-top-nav.html'));
    });

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
