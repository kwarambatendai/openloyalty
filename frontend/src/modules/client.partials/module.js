const MODULE_NAME = 'client.partials';

angular.module(MODULE_NAME, [])
    .run(($templateCache) => {
        $templateCache.put('templates/client-footer.html', require('./templates/client-footer.html'));
        $templateCache.put('templates/client-top-nav.html', require('./templates/client-top-nav.html'));
    });

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
