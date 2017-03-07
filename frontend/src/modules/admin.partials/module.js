const MODULE_NAME = 'admin.partials';

angular.module(MODULE_NAME, [])
    .run(($templateCache, $http) => {
        let catchErrorTemplate = () => {
            throw `${MODULE_NAME} has missing template`
        };

        $http.get(`./build/${MODULE_NAME}/templates/footer.html`)
            .then(
                response => {
                    $templateCache.put('./templates/footer.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/left-nav.html`)
            .then(
                response => {
                    $templateCache.put('./templates/left-nav.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/right-nav.html`)
            .then(
                response => {
                    $templateCache.put('./templates/right-nav.html', response.data);
                }
            )
            .catch(catchErrorTemplate);

        $http.get(`./build/${MODULE_NAME}/templates/top-nav.html`)
            .then(
                response => {
                    $templateCache.put('./templates/top-nav.html', response.data);
                }
            )
            .catch(catchErrorTemplate);
    })

try {
    window.OpenLoyaltyConfig.modules.push(MODULE_NAME);
} catch(err) {
    throw `${MODULE_NAME} will not be registered`
}