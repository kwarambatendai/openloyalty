<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\SegmentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Bundle\PosBundle\DataFixtures\ORM\LoadPosData;
use OpenLoyalty\Domain\Segment\Command\CreateSegment;
use OpenLoyalty\Domain\Segment\Model\Criteria\Anniversary;
use OpenLoyalty\Domain\Segment\Model\Criterion;
use OpenLoyalty\Domain\Segment\SegmentId;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;

/**
 * Class LoadSegmentData.
 */
class LoadSegmentData extends ContainerAwareFixture implements OrderedFixtureInterface
{
    const SEGMENT_ID = '00000000-0000-0000-0000-000000000000';
    const SEGMENT2_ID = '00000000-0000-0000-0000-000000000002';
    const SEGMENT3_ID = '00000000-0000-0000-0000-000000000003';
    const SEGMENT4_ID = '00000000-0000-0000-0000-000000000004';
    const SEGMENT5_ID = '00000000-0000-0000-0000-000000000005';
    const SEGMENT6_ID = '00000000-0000-0000-0000-000000000006';
    const SEGMENT7_ID = '00000000-0000-0000-0000-000000000007';
    const SEGMENT8_ID = '00000000-0000-0000-0000-000000000008';
    const SEGMENT9_ID = '00000000-0000-0000-0000-000000000009';

    public function load(ObjectManager $manager)
    {
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateSegment(new SegmentId(self::SEGMENT_ID), [
                    'name' => 'test',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'segmentPartId' => '00000000-0000-0000-0000-000000000000',
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_BOUGHT_IN_POS,
                                    'criterionId' => '00000000-0000-0000-0000-000000000000',
                                    'posIds' => [LoadPosData::POS_ID],
                                ],
                                [
                                    'type' => Criterion::TYPE_AVERAGE_TRANSACTION_AMOUNT,
                                    'criterionId' => '00000000-0000-0000-0000-000000000001',
                                    'fromAmount' => 1,
                                    'toAmount' => 10000,
                                ],
                                [
                                    'type' => Criterion::TYPE_TRANSACTION_COUNT,
                                    'criterionId' => '00000000-0000-0000-0000-000000000002',
                                    'min' => 10,
                                    'max' => 20,
                                ],
                            ],
                        ],
                    ],
                ])
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateSegment(new SegmentId(self::SEGMENT2_ID), [
                    'name' => 'anniversary',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'segmentPartId' => '00000000-0000-0000-0000-000000000001',
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_ANNIVERSARY,
                                    'criterionId' => '00000000-0000-0000-0000-000000000011',
                                    'days' => 10,
                                    'anniversaryType' => Anniversary::TYPE_BIRTHDAY,
                                ],
                            ],
                        ],
                    ],
                ])
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateSegment(new SegmentId(self::SEGMENT3_ID), [
                    'name' => 'purchase period',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'segmentPartId' => '00000000-0000-0000-0000-000000000033',
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_PURCHASE_PERIOD,
                                    'criterionId' => '00000000-0000-0000-0000-000000000033',
                                    'fromDate' => new \DateTime('2014-12-01'),
                                    'toDate' => new \DateTime('2015-01-01'),
                                ],
                            ],
                        ],
                    ],
                ])
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateSegment(new SegmentId(self::SEGMENT4_ID), [
                    'name' => 'last purchase 10 days ago',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'segmentPartId' => '00000000-0000-0000-0000-000000000044',
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_LAST_PURCHASE_N_DAYS_BEFORE,
                                    'criterionId' => '00000000-0000-0000-0000-000000000045',
                                    'days' => 10,
                                ],
                            ],
                        ],
                    ],
                ])
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateSegment(new SegmentId(self::SEGMENT5_ID), [
                    'name' => 'transaction amount 10-50',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'segmentPartId' => '00000000-0000-0000-0000-000000000055',
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_TRANSACTION_AMOUNT,
                                    'criterionId' => '00000000-0000-0000-0000-000000000055',
                                    'fromAmount' => 10,
                                    'toAmount' => 50,
                                ],
                            ],
                        ],
                    ],
                ])
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateSegment(new SegmentId(self::SEGMENT6_ID), [
                    'name' => '10 percent in pos',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'segmentPartId' => '00000000-0000-0000-0000-000000000066',
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_TRANSACTION_PERCENT_IN_POS,
                                    'criterionId' => '00000000-0000-0000-0000-000000000066',
                                    'percent' => 0.10,
                                    'posId' => LoadPosData::POS_ID,
                                ],
                            ],
                        ],
                    ],
                ])
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateSegment(new SegmentId(self::SEGMENT7_ID), [
                    'name' => 'bought skus',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'segmentPartId' => '00000000-0000-0000-0000-000000000077',
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_BOUGHT_SKUS,
                                    'criterionId' => '00000000-0000-0000-0000-000000000077',
                                    'skuIds' => ['SKU1'],
                                ],
                            ],
                        ],
                    ],
                ])
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateSegment(new SegmentId(self::SEGMENT8_ID), [
                    'name' => 'bought makers',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'segmentPartId' => '00000000-0000-0000-0000-000000000088',
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_BOUGHT_MAKERS,
                                    'criterionId' => '00000000-0000-0000-0000-000000000099',
                                    'makers' => ['company'],
                                ],
                            ],
                        ],
                    ],
                ])
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateSegment(new SegmentId(self::SEGMENT9_ID), [
                    'name' => 'bought labels',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'segmentPartId' => '00000000-0000-0000-0000-000000000099',
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_BOUGHT_LABELS,
                                    'criterionId' => '00000000-0000-0000-0000-000000000999',
                                    'labels' => [
                                        ['key' => 'test', 'value' => 'label'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ])
            );
    }

    public function getOrder()
    {
        return 99;
    }
}
