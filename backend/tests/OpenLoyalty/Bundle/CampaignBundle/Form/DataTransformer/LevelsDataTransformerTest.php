<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Form\DataTransformer;

use OpenLoyalty\Domain\Campaign\LevelId;

/**
 * Class LevelsDataTransformerTest.
 */
class LevelsDataTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_transforms()
    {
        $data = [
            new LevelId('000096cf-32a3-43bd-9034-4df343e5fd93'),
            new LevelId('000096cf-32a3-43bd-9034-4df343e5fd91'),
            new LevelId('000096cf-32a3-43bd-9034-4df343e5fd90'),
        ];
        $transformer = new LevelsDataTransformer();
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
        $transformer = new LevelsDataTransformer();
        $transformed = $transformer->reverseTransform($data);
        $this->assertEquals([
            new LevelId('000096cf-32a3-43bd-9034-4df343e5fd93'),
            new LevelId('000096cf-32a3-43bd-9034-4df343e5fd91'),
            new LevelId('000096cf-32a3-43bd-9034-4df343e5fd90'),
        ], $transformed);
    }
}
