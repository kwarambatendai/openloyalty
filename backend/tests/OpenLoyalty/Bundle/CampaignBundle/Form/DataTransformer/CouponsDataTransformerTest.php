<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Form\DataTransformer;

use OpenLoyalty\Domain\Campaign\Model\Coupon;

/**
 * Class CouponsDataTransformerTest.
 */
class CouponsDataTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_transforms()
    {
        $data = [
            new Coupon('000096cf-32a3-43bd-9034-4df343e5fd93'),
            new Coupon('000096cf-32a3-43bd-9034-4df343e5fd91'),
            new Coupon('000096cf-32a3-43bd-9034-4df343e5fd90'),
        ];
        $transformer = new CouponsDataTransformer();
        $transformed = $transformer->transform($data);
        $this->assertEquals([
            '000096cf-32a3-43bd-9034-4df343e5fd93',
            '000096cf-32a3-43bd-9034-4df343e5fd91',
            '000096cf-32a3-43bd-9034-4df343e5fd90',
        ], $transformed);
    }

    /**
     * @test
     */
    public function it_reverse_transforms()
    {
        $data = [
            '000096cf-32a3-43bd-9034-4df343e5fd93',
            '000096cf-32a3-43bd-9034-4df343e5fd91',
            '000096cf-32a3-43bd-9034-4df343e5fd90',
        ];
        $transformer = new CouponsDataTransformer();
        $transformed = $transformer->reverseTransform($data);
        $this->assertEquals([
            new Coupon('000096cf-32a3-43bd-9034-4df343e5fd93'),
            new Coupon('000096cf-32a3-43bd-9034-4df343e5fd91'),
            new Coupon('000096cf-32a3-43bd-9034-4df343e5fd90'),
        ], $transformed);
    }
}
