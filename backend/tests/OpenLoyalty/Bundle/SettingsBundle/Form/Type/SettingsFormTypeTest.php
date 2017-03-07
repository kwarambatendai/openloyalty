<?php

namespace OpenLoyalty\Bundle\SettingsBundle\Form\Type;

use OpenLoyalty\Bundle\SettingsBundle\Entity\BooleanSettingEntry;
use OpenLoyalty\Bundle\SettingsBundle\Entity\IntegerSettingEntry;
use OpenLoyalty\Bundle\SettingsBundle\Entity\JsonSettingEntry;
use OpenLoyalty\Bundle\SettingsBundle\Entity\StringSettingEntry;
use OpenLoyalty\Bundle\SettingsBundle\Model\Settings;
use OpenLoyalty\Bundle\SettingsBundle\Model\TranslationsEntry;
use OpenLoyalty\Bundle\SettingsBundle\Service\TranslationsProvider;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class SettingsFormTypeTest.
 */
class SettingsFormTypeTest extends TypeTestCase
{
    protected $stringEntries = [
        'currency' => 'pln',
        'defaultFrontendTranslations' => 'english.json',
        'timezone' => 'Europe/Berlin',
        'programName' => 'test',
        'programConditionsUrl' => 'url',
        'programFaqUrl' => 'faq',
        'programUrl' => 'program url',
        'programPointsSingular' => 'p',
        'programPointsPlural' => 'ps',
        'helpEmailAddress' => 'email',
        'tierAssignType' => 'points',
    ];

    protected $booleanEntries = [
        'returns' => true,
        'allTimeActive' => true,
        'excludeDeliveryCostsFromTierAssignment' => true,
    ];

    protected $integerEntries = [
        'pointsDaysActive' => 10,
    ];

    private $settingsManager;

    /**
     * @var TranslationsProvider
     */
    private $transaltionsProvider;

    private $validator;

    protected function setUp()
    {
        $this->transaltionsProvider = $this->getMockBuilder(TranslationsProvider::class)->disableOriginalConstructor()
            ->getMock();
        $this->transaltionsProvider->method('getAvailableTranslationsList')->willReturn([
            new TranslationsEntry('english.json'),
        ]);

        $this->settingsManager = $this->getMock('OpenLoyalty\Bundle\SettingsBundle\Service\SettingsManager');
        $this->settingsManager->method('getSettingByKey')->willReturn(null);
        $this->validator = $this->getMock(
            'Symfony\Component\Validator\Validator\ValidatorInterface'
        );
        $this->validator
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));
        $metadata = $this->getMockBuilder('Symfony\Component\Validator\Mapping\ClassMetadata')
                    ->disableOriginalConstructor()->getMock();
        $metadata->method('addConstraint')->willReturn(true);
        $metadata->method('addPropertyConstraint')->willReturn(true);

        $this->validator->method('getMetadataFor')->willReturn(
            $metadata
        );

        parent::setUp();
    }

    protected function getExtensions()
    {
        $type = new SettingsFormType($this->settingsManager, $this->transaltionsProvider);

        return array(
            new PreloadedExtension(array($type), array()),
            new ValidatorExtension($this->validator),
        );
    }

    /**
     * @test
     */
    public function it_has_valid_data_after_submit()
    {
        $form = $this->factory->create(SettingsFormType::class);

        $object = new Settings();

        foreach ($this->stringEntries as $key => $value) {
            $entry = new StringSettingEntry($key);
            $entry->setValue($value);
            $object->addEntry($entry);
        }
        foreach ($this->booleanEntries as $key => $value) {
            $entry = new BooleanSettingEntry($key);
            $entry->setValue($value);
            $object->addEntry($entry);
        }
        foreach ($this->integerEntries as $key => $value) {
            $entry = new IntegerSettingEntry($key);
            $entry->setValue($value);
            $object->addEntry($entry);
        }

        $entry = new JsonSettingEntry('customersIdentificationPriority');
        $entry->setValue([
            ['field' => 'email', 'priority' => 1],
        ]);
        $object->addEntry($entry);
        $entry = new JsonSettingEntry('excludedDeliverySKUs');
        $entry->setValue([
            '123',
        ]);
        $object->addEntry($entry);
        $entry = new JsonSettingEntry('excludedLevelSKUs');
        $entry->setValue([
            '123',
        ]);
        $object->addEntry($entry);
        $entry = new JsonSettingEntry('excludedLevelCategories');
        $entry->setValue([
            '123',
        ]);
        $object->addEntry($entry);

        $formData = array_merge($this->stringEntries, $this->booleanEntries, $this->integerEntries, [
            'customersIdentificationPriority' => [
                ['field' => 'email', 'priority' => 1],
            ],
            'excludedDeliverySKUs' => [
                '123'
            ],
            'excludedLevelSKUs' => [
                '123'
            ],
            'excludedLevelCategories' => [
                '123'
            ],
        ]);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
