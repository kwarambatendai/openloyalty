<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\EarningRuleBundle\Event\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use OpenLoyalty\Bundle\EarningRuleBundle\Model\EarningRule;
use OpenLoyalty\Domain\EarningRule\CustomEventEarningRule;
use OpenLoyalty\Domain\EarningRule\PointsEarningRule;
use OpenLoyalty\Domain\Model\SKU;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class EarningRuleSerializationListener.
 */
class EarningRuleSerializationListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * EarningRuleSerializationListener constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var EarningRule $rule */
        $rule = $event->getObject();

        if ($rule instanceof PointsEarningRule) {
            $event->getVisitor()->addData('excludedSKUs', array_map(function (SKU $sku) {
                return $sku->getCode();
            }, $rule->getExcludedSKUs()));
        }
        if ($rule instanceof CustomEventEarningRule) {
            $event->getVisitor()->setData(
                'usageUrl',
                $this->urlGenerator->generate('oloy.earning_rule.report_custom_event', [
                    'customer' => ':customerId',
                    'eventName' => $rule->getEventName(),
                ], UrlGeneratorInterface::ABSOLUTE_URL)
            );
        }
    }
}
