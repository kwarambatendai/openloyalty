Architecture
============

Main concepts used in this project are

* `CQRS <https://martinfowler.com/bliki/CQRS.html>`_
* DDD (Domain Driven Design)
* `Event sourcing <https://martinfowler.com/eaaDev/EventSourcing.html>`_

Event sourcing is used in order to have full history of domain events in system. Specially useful for transactions.
Events are stored in database and read models are created and stored in elasticsearch for better performance.
Not all parts of system need to be written using event sourcing concept so e.g. campaign component is not using it.

Components
----------

In order to fulfill Domain Driven Design requirements, whole domain related things were separated into bounded context - components.

Complete list of components:

* `Account <./components/account.rst>`_
* `Audit <./components/audit.rst>`_
* `Campaign <./components/campaign.rst>`_
* `Customer <./components/customer.rst>`_
* `EarningRule <./components/earning_rule.rst>`_
* `Email <./components/email.rst>`_
* `Level <./components/level.rst>`_
* `Pos <./components/pos.rst>`_
* `Segment <./components/segment.rst>`_
* `Seller <./components/seller.rst>`_
* `Transaction <./components/transaction.rst>`_

Bundles
-------

Whole domain is utilized in the bundles.

Complete list of bundles:

* `AnalyticsBundle <./bundles/analytics.rst>`_
* `AuditBundle <./bundles/audit.rst>`_
* `CampaignBundle <./bundles/campaign.rst>`_
* `EarningRuleBundle <./bundles/earning_rule.rst>`_
* `EmailBundle <./bundles/email.rst>`_
* `LevelBundle <./bundles/level.rst>`_
* `PaginationBundle <./bundles/pagination.rst>`_
* `PluginBundle <./bundles/plugin.rst>`_
* `PointsBundle <./bundles/points.rst>`_
* `PosBundle <./bundles/pos.rst>`_
* `SegmentBundle <./bundles/segment.rst>`_
* `SettingsBundle <./bundles/settings.rst>`_
* `TransactionBundle <./bundles/transaction.rst>`_
* `UserBundle <./bundles/user.rst>`_
* `UtilityBundle <./bundles/utility.rst>`_

Infrastructure
--------------
Things that are not strict part of particular domain or are related to system infrastructure are defined in `Infrastructure` directory.
There are such things like Doctrine ORM mapping files, repositories, types and some system event listener.