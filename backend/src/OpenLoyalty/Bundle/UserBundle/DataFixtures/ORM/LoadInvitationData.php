<?php

namespace OpenLoyalty\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Domain\Customer\Command\CreateInvitation;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\InvitationId;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadInvitationData.
 */
class LoadInvitationData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    const INVITATION_ID_1 = '22200000-0000-474c-b092-b0dd880c07e1';
    const INVITATION_ID_2 = '22200000-0000-474c-b092-b0dd880c07e2';
    const INVITATION_ID_3 = '22200000-0000-474c-b092-b0dd880c07e3';
    const INVITATION_ID_4 = '22200000-0000-474c-b092-b0dd880c07e4';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $bus = $this->container->get('broadway.command_handling.command_bus');

        for ($i = 1; $i <= 4; ++$i) {
            $bus->dispatch(
                new CreateInvitation(
                    new InvitationId(constant(self::class.'::INVITATION_ID_'.$i)),
                    new CustomerId(LoadUserData::USER_USER_ID),
                    'test'.$i.'@oloy.com'
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
