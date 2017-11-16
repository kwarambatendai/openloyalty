const MODULE_NAME = 'admin.partials';

angular.module(MODULE_NAME, [])
    .run(($templateCache) => {
        $templateCache.put('templates/footer.html', require('./templates/footer.html'));
        $templateCache.put('templates/left-nav.html', require('./templates/left-nav.html'));
        $templateCache.put('templates/right-nav.html', require('./templates/right-nav.html'));
        $templateCache.put('templates/top-nav.html', require('./templates/top-nav.html'));
    });

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}
