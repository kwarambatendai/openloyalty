<?php

namespace OpenLoyalty\Bundle\EarningRuleBundle\Form\Type;

use OpenLoyalty\Bundle\EarningRuleBundle\Model\EarningRule;
use OpenLoyalty\Domain\Model\SKU;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class CreateEarningRuleFormTypeTest.
 */
class CreateEarningRuleFormTypeTest extends TypeTestCase
{
    private $validator;

    protected function setUp()
    {
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
        $type = new CreateEarningRuleFormType();

        return array(
            new PreloadedExtension(array($type), array()),
            new ValidatorExtension($this->validator),
        );
    }

    /**
     * @test
     */
    public function it_has_valid_data_when_creating_new_event_earning_rule()
    {
        $formData = array_merge($this->getMainData(), [
            'type' => EarningRule::TYPE_EVENT,
            'eventName' => 'test event',
            'pointsAmount' => 100,
        ]);

        $form = $this->factory->create(CreateEarningRuleFormType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    protected function getMainData()
    {
        return [
            'name' => 'test',
            'description' => 'sth',
            'startAt' => '2016-08-01',
            'endAt' => '2016-10-10',
            'active' => false,
            'allTimeActive' => false,
        ];
    }
}
