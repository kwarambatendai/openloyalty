<?php

namespace OpenLoyalty\Bundle\SettingsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Bundle\SettingsBundle\Entity\BooleanSettingEntry;
use OpenLoyalty\Bundle\SettingsBundle\Entity\IntegerSettingEntry;
use OpenLoyalty\Bundle\SettingsBundle\Entity\JsonSettingEntry;
use OpenLoyalty\Bundle\SettingsBundle\Entity\StringSettingEntry;
use OpenLoyalty\Bundle\SettingsBundle\Form\Type\CustomersIdentificationPriority;
use OpenLoyalty\Bundle\SettingsBundle\Model\Settings;
use OpenLoyalty\Infrastructure\Customer\TierAssignTypeProvider;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadSettingsData.
 */
class LoadSettingsData extends ContainerAwareFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $settings = new Settings();

        $currency = new StringSettingEntry('currency', 'eur');
        $settings->addEntry($currency);

        $timezone = new StringSettingEntry('timezone', 'Europe/Warsaw');
        $settings->addEntry($timezone);

        $programName = new StringSettingEntry('programName', 'Loyalty Program');
        $settings->addEntry($programName);

        $pointsSingular = new StringSettingEntry('programPointsSingular', 'Point');
        $settings->addEntry($pointsSingular);

        $pointsPlural = new StringSettingEntry('programPointsPlural', 'Points');
        $settings->addEntry($pointsPlural);

        $pointsDaysActive = new IntegerSettingEntry('pointsDaysActive', 30);
        $settings->addEntry($pointsDaysActive);

        $returns = new BooleanSettingEntry('returns', true);
        $settings->addEntry($returns);

        $entry = new StringSettingEntry('tierAssignType');
        $entry->setValue(TierAssignTypeProvider::TYPE_TRANSACTIONS);
        $settings->addEntry($entry);

        $entry3 = new JsonSettingEntry('excludedLevelCategories');
        $entry3->setValue(['category_excluded_from_level']);
        $settings->addEntry($entry3);

        $priority = new JsonSettingEntry('customersIdentificationPriority');
        $priorities = [
            [
                'priority' => 2,
                'field' => 'loyaltyCardNumber',
            ],
            [
                'priority' => 1,
                'field' => 'email',
            ],
        ];
        $priority->setValue($priorities);
        $settings->addEntry($priority);

        $defaultFrontendTranslations = new StringSettingEntry('defaultFrontendTranslations');
        $defaultFrontendTranslations->setValue('english.json');
        $settings->addEntry($defaultFrontendTranslations);

        $this->getContainer()->get('ol.settings.manager')->save($settings);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get the order of this fixture.
     *
     * @return int
     */
    public function getOrder()
    {
        return 0;
    }
}
