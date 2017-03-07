<?php

namespace OpenLoyalty\Bundle\UserBundle\EventListener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use OpenLoyalty\Bundle\SettingsBundle\Service\SettingsManager;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelId;
use OpenLoyalty\Domain\Level\LevelRepository;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosId;
use OpenLoyalty\Domain\Pos\PosRepository;
use PhpOption\None;

/**
 * Class CustomerSerializationListener.
 */
class CustomerSerializationListener implements EventSubscriberInterface
{
    /**
     * @var LevelRepository
     */
    protected $levelRepository;

    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * @var PosRepository
     */
    protected $posRepository;

    /**
     * CustomerSerializationListener constructor.
     *
     * @param LevelRepository $levelRepository
     * @param SettingsManager $settingsManager
     * @param PosRepository   $posRepository
     */
    public function __construct(
        LevelRepository $levelRepository,
        SettingsManager $settingsManager,
        PosRepository $posRepository
    ) {
        $this->levelRepository = $levelRepository;
        $this->settingsManager = $settingsManager;
        $this->posRepository = $posRepository;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var CustomerDetails $customer */
        $customer = $event->getObject();

        if ($customer instanceof CustomerDetails) {
            $currency = $this->settingsManager->getSettingByKey('currency');
            $currency = $currency ? $currency->getValue() : 'PLN';
            $event->getVisitor()->addData('currency', $currency);

            $context = $event->getContext();
            $option = $context->attributes->get('customerSegments');
            if ($option && !$option instanceof None) {
                $segments = $context->attributes->get('customerSegments')->get();
                $event->getVisitor()->addData('segments', $segments);
            }

            if ($customer->getLevelId()) {
                $level = $this->levelRepository->byId(new LevelId($customer->getLevelId()->__toString()));
                if ($level instanceof Level && $level->getReward()) {
                    $event->getVisitor()->addData('levelPercent', number_format($level->getReward()->getValue() * 100, 2).'%');
                }
            }

            if ($customer->getPosId()) {
                $pos = $this->posRepository->byId(new PosId($customer->getPosId()->__toString()));
                if ($pos instanceof Pos) {
                    $event->getVisitor()->addData('posIdentifier', $pos->getIdentifier());
                }
            }
        }
    }
}
