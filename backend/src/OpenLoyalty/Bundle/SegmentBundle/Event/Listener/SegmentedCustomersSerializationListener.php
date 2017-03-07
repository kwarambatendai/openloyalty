<?php

namespace OpenLoyalty\Bundle\SegmentBundle\Event\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\GenericSerializationVisitor;
use OpenLoyalty\Domain\Segment\ReadModel\SegmentedCustomers;
use PhpOption\None;

/**
 * Class SegmentedCustomersSerializationListener.
 */
class SegmentedCustomersSerializationListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var SegmentedCustomers $segment */
        $segment = $event->getObject();

        if ($segment instanceof SegmentedCustomers) {
            $context = $event->getContext();
            $option = $context->attributes->get('customersDetails');
            if (!$option || $option instanceof None) {
                return;
            }
            $details = $context->attributes->get('customersDetails')->get();
            if (!isset($details[$segment->getCustomerId()->__toString()])) {
                return;
            }

            $details = $details[$segment->getCustomerId()->__toString()];
            $visitor = $context->getVisitor();

            if ($visitor instanceof GenericSerializationVisitor) {
                foreach ($details as $key => $value) {
                    if ($visitor->hasData($key)) {
                        continue;
                    }
                    $visitor->addData($key, $value);
                }
            }
        }
    }
}
