<?php

namespace OpenLoyalty\Bundle\DemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Domain\Segment\Command\ActivateSegment;
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
    const SEGMENT_ID_BLACK = '00000000-0000-0000-0000-000000000012';
    const SEGMENT2_ID = '00000000-0000-0000-0000-000000000002';
    const SEGMENT3_ID = '00000000-0000-0000-0000-000000000003';
    const SEGMENT4_ID = '00000000-0000-0000-0000-000000000004';
    const SEGMENT5_ID = '00000000-0000-0000-0000-000000000005';
    const SEGMENT6_ID = '00000000-0000-0000-0000-000000000006';
    const SEGMENT7_ID = '00000000-0000-0000-0000-000000000007';
    const SEGMENT8_ID = '00000000-0000-0000-0000-000000000008';
    const SEGMENT9_ID = '00000000-0000-0000-0000-000000000009';
    const SEGMENT10_ID = '00000000-0000-0000-0000-000000000010';
    const SEGMENT_ID_GIFT_SHOPPERS = '00000000-0000-0000-0000-000000000040';
    const SEGMENT_MANY_ORDERS = '00000000-0000-0000-0000-000000000050';
    const SEGMENT_MANY_REGISTRATION = '00000000-0000-0000-0000-000000000060';
    const SEGMENT_ONE_TIMERS = '00000000-0000-0000-0000-000000000070';
    const SEGMENT_REGISTRATION = '00000000-0000-0000-0000-000000000100';

    public function load(ObjectManager $manager)
    {
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->newCustomersForSpecificPosData()
            );

        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT_ID))
            );
        $startDate = \DateTime::createFromFormat('Y-m-d H:i', '2016-11-25 01:00');
        $endDate = \DateTime::createFromFormat('Y-m-d H:i', '2016-11-25 23:00');
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getBlackFridayOfflineCustomers($startDate, $endDate)
            );

        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT_ID_BLACK))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getBirthdayAnniversaryCustomers()
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT2_ID))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getRegistrationAnniversaryCustomers()
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT_REGISTRATION))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getNovemberCustomers()
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT3_ID))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getOneOrMoreOrdersCustomers()
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT_MANY_ORDERS))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getOneTimeBuyers()
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT_ONE_TIMERS))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getLeavingCustomers()
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT_ONE_TIMERS))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getBigSpenders()
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT5_ID))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getGroundShopCustomers()
            );

        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT6_ID))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getSpecificProductCustomers()
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT7_ID))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getCustomersLoyalTo()
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT8_ID))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getChristmasGiftShoppers()
            );

        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT9_ID))
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                $this->getCustomersWithAow()
            );
        $this->container
            ->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment(new SegmentId(self::SEGMENT10_ID))
            );
    }

    public function getOrder()
    {
        return 1;
    }

    /**
     * @return CreateSegment
     */
    public function newCustomersForSpecificPosData()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT_ID),
            [
                'name' => 'New customer from specific POS',
                'description' => 'Customers who registered and bought in off-line store',
                'active' => true,
                'parts' => [
                    [
                        'segmentPartId' => '425a5a52-1e0b-4134-9529-08e2d0f5c8fc',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_TRANSACTION_COUNT,
                                'criterionId' => 'b6e003c7-c414-423a-8b74-8313c94f03bd',
                                'min' => 1,
                                'max' => 1,
                            ],
                        ],
                    ],
                    [
                        'segmentPartId' => 'd1d7023d-cf43-4597-9e1f-f2c23812289a',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_BOUGHT_IN_POS,
                                'criterionId' => '4f1108f7-facb-41bc-b9ae-b09ae81cede2',
                                'posIds' => ['00000000-0000-474c-1111-b0dd880c87c2'],
                            ],
                        ],

                    ],
                ],
            ]
        );
    }

    /**
     * @param $startDate
     * @param $endDate
     *
     * @return CreateSegment
     */
    public function getBlackFridayOfflineCustomers($startDate, $endDate)
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT_ID_BLACK),
            [
                'name' => 'Black Friday off-line customers',
                'description' => 'Customers who bought something during  Black Friday in off-line stores',
                'active' => true,
                'parts' => [
                    [
                        'segmentPartId' => 'f19144e8-9da1-439c-8f4e-e2ad45d5486f',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_PURCHASE_PERIOD,
                                'criterionId' => '491e8871-3bf8-4ab6-bb96-0c8e3a1749af',
                                'fromDate' => $startDate,
                                'toDate' => $endDate,
                            ],
                        ],
                    ],
                    [
                        'segmentPartId' => 'f9361195-a86f-41bf-a910-b80e4b8c7990',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_BOUGHT_IN_POS,
                                'criterionId' => 'b8d95d11-6f56-4488-8fc2-b683831c67dc',
                                'posIds' => [
                                    '00000000-0000-474c-1111-b0dd880c07e2',
                                    '00000000-0000-474c-1111-b0dd880c87c2',
                                ],
                            ],
                        ],

                    ],
                ],
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getBirthdayAnniversaryCustomers()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT2_ID),
            [
                'name' => 'Birthday anniversary',
                'description' => 'Show customers with less than 10 days to birthday',
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
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getRegistrationAnniversaryCustomers()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT_REGISTRATION),
            [
                'name' => 'Registration anniversary',
                'description' => 'Customers with registration anniversary in 5 days',
                'active' => true,
                'parts' => [
                    [
                        'segmentPartId' => '07b00c19-eb2c-42cf-ace2-83b4b245a33c',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_ANNIVERSARY,
                                'criterionId' => '9d762f23-4c99-42ca-a469-cdd1ec124686',
                                'days' => 5,
                                'anniversaryType' => Anniversary::TYPE_REGISTRATION,
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getNovemberCustomers()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT3_ID),
            [
                'name' => 'Purchase period',
                'description' => 'Show customers with purchases in November',
                'active' => true,
                'parts' => [
                    [
                        'segmentPartId' => 'c313e2c9-e80c-4917-bb89-253c8e94e428',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_PURCHASE_PERIOD,
                                'criterionId' => '1f4c44e1-ab8c-41fd-a842-53f0d82ab172',
                                'fromDate' => new \DateTime('2014-11-01'),
                                'toDate' => new \DateTime('2015-12-01'),
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getOneOrMoreOrdersCustomers()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT_MANY_ORDERS),
            [
                'name' => 'One or more orders',
                'description' => 'Customers who bought anything',
                'active' => true,
                'parts' => [
                    [
                        'segmentPartId' => 'dbf236a2-a782-4e7b-b64b-ee3d30cebed3',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_TRANSACTION_COUNT,
                                'criterionId' => 'eaef3a25-cf1c-4a1a-89a4-e08d5b564a7a',
                                'min' => 1,
                                'max' => 9999999,
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getOneTimeBuyers()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT_ONE_TIMERS),
            [
                'name' => 'One time buyers',
                'description' => 'Customers who bought only once',
                'active' => true,
                'parts' => [
                    [
                        'segmentPartId' => 'e3c1d111-af0f-4721-a7d8-83bcfe1a641a',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_TRANSACTION_COUNT,
                                'criterionId' => 'f74e2476-a49c-4777-858c-8b39454e273f',
                                'min' => 1,
                                'max' => 1,
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getLeavingCustomers()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT4_ID),
            [
                'name' => 'Leaving customers',
                'description' => 'Customers with last purchase 10 days ago',
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
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getBigSpenders()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT5_ID),
            [
                'name' => 'Big spenders',
                'description' => 'Customers with CLV between 1000 and 2500',
                'active' => true,
                'parts' => [
                    [
                        'segmentPartId' => '00000000-0000-0000-0000-000000000055',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_TRANSACTION_AMOUNT,
                                'criterionId' => '00000000-0000-0000-0000-000000000055',
                                'fromAmount' => 1000,
                                'toAmount' => 2500,
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getGroundShopCustomers()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT6_ID),
            [
                'name' => 'Ground shop customers',
                'description' => 'Customers who buy less than 40% in ground store',
                'parts' => [
                    [
                        'segmentPartId' => '00000000-0000-0000-0000-000000000066',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_TRANSACTION_PERCENT_IN_POS,
                                'criterionId' => '00000000-0000-0000-0000-000000000066',
                                'percent' => 0.40,
                                'posId' => LoadPosData::POS2_ID3,
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getSpecificProductCustomers()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT7_ID),
            [
                'name' => 'Customers who bought specific products',
                'description' => 'Customer who bought products with SKUs Pmp00123, hbm001, ams003',
                'active' => true,
                'parts' => [
                    [
                        'segmentPartId' => '00000000-0000-0000-0000-000000000077',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_BOUGHT_SKUS,
                                'criterionId' => '00000000-0000-0000-0000-000000000077',
                                'skuIds' => [
                                    'Pmp00136',
                                    'hbm001',
                                    'ams003',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getCustomersLoyalTo()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT8_ID),
            [
                'name' => 'Customers loyal to 7 For All Mankind',
                'description' => 'Customers buing products of 7 For All Mankind',
                'parts' => [
                    [
                        'segmentPartId' => '00000000-0000-0000-0000-000000000088',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_BOUGHT_MAKERS,
                                'criterionId' => '00000000-0000-0000-0000-000000000099',
                                'makers' => ['7 For All Mankind'],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getChristmasGiftShoppers()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT9_ID),
            [
                'name' => 'Christmas gift shoppers',
                'description' => 'Customers who bought products with label "for christmas-present"',
                'active' => true,
                'parts' => [
                    [
                        'segmentPartId' => '00000000-0000-0000-0000-000000000099',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_BOUGHT_LABELS,
                                'criterionId' => '00000000-0000-0000-0000-000000000999',
                                'labels' => [
                                    ['key' => 'promotion', 'value' => 'for-christmas-present'],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @return CreateSegment
     */
    public function getCustomersWithAow()
    {
        return new CreateSegment(
            new SegmentId(self::SEGMENT10_ID),
            [
                'name' => 'Customers with AOV',
                'description' => 'Customer with AOV between 500 and 600',
                'active' => true,
                'parts' => [
                    [
                        'segmentPartId' => '00000000-0000-0000-0000-000000010000',
                        'criteria' => [
                            [
                                'type' => Criterion::TYPE_AVERAGE_TRANSACTION_AMOUNT,
                                'criterionId' => '00000000-0000-0000-0000-000000010001',
                                'fromAmount' => 500,
                                'toAmount' => 600,
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}
