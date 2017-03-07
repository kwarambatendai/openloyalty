---
Plugin Bundle is a extenstion point for plugins. 

As far as standard Symfony structure of bundle will be used autoloader will catch every part of bundle except routing - thanks to changes in AppKernel. 

Here is only one point of interest - form manager. By default it catches all form types with same name as bundle name, to load them as a main config point. 

Of course You can also get those via get route. 

Example of bundle location:

src/OpenLoyaltyPlugin/AppBundle

Example of form location:

src/OpenLoyaltyPlugin/AppBundle/Form/Type/AppBundleFormType.php



