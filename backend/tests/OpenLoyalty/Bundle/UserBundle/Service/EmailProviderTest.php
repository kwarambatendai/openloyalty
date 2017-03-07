<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

use OpenLoyalty\Bundle\EmailBundle\Model\MessageInterface;
use OpenLoyalty\Bundle\EmailBundle\Service\MessageFactoryInterface;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Bundle\EmailBundle\Mailer\OloyMailer;
use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Customer\Model\Coupon;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\Model\Reward;
use PHPUnit_Framework_MockObject_MockBuilder;
use PHPUnit_Framework_MockObject_MockObject;

class EmailProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OloyMailer|PHPUnit_Framework_MockObject_MockObject
     */
    private $mailer;

    /**
     * @var MessageFactoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $messageFactory;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var MessageInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $message;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mailer = $this->getMockBuilder(OloyMailer::class)->disableOriginalConstructor()->getMock();
        $this->message = $this->getMockBuilder(MessageInterface::class)->disableOriginalConstructor()->getMock();
        $this->messageFactory = $this->getMockBuilder(MessageFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageFactory->method('create')->willReturn($this->message);

        $this->parameters = [
            'from_address' => 'from@mail.com',
            'from_name' => 'from name',
            'password_reset_url' => 'http://url.test/pass/reset',
            'loyalty_program_name' => 'Test program',
            'ecommerce_address' => 'http://ecommerce.test',
            'customer_panel_url' => 'http://customer.panel',
        ];
    }

    /**
     * @test
     * @dataProvider emailMessageProvider
     *
     * @param string $subject
     * @param string $email
     * @param string $template
     * @param array  $params
     */
    public function it_sends_message(string $subject, string $email, string $template = null, array $params = [])
    {
        $this->messageFactory->expects($this->once())->method('create')->willReturn($this->message);

        $this->message->expects($this->once())->method('setSubject')->with($subject);
        $this->message->expects($this->once())->method('setRecipientEmail')->with($email);
        $this->message->expects($this->once())->method('setRecipientName')->with($email);
        $this->message->expects($this->once())->method('setSenderEmail')->with($this->parameters['from_address']);
        $this->message->expects($this->once())->method('setSenderName')->with($this->parameters['from_name']);
        $this->message->expects($this->once())->method('setTemplate')->with($template);
        $this->message->expects($this->once())->method('setParams')->with($params);

        $this->getEmailProviderMock(null, ['sendMessage'])
             ->sendMessage($subject, $email, $template, $params);
    }

    /**
     * @return array
     */
    public function emailMessageProvider()
    {
        return [
            ['subject', 'example@example.com', 'template', ['params']],
            ['subject', 'example@example.com'],
        ];
    }

    /**
     * @test
     */
    public function it_sends_registration_with_temporary_password_mail()
    {
        $user = $this->getCustomerDetailsMock();
        $user->expects($this->exactly(2))->method('getEmail')->willReturn('example@example.com');
        $user->expects($this->atLeastOnce())->method('getPhone')->willReturn('123455668990');
        $user->expects($this->atLeastOnce())->method('getLoyaltyCardNumber')->willReturn('aaabbbccc');

        $emailProvider = $this->getEmailProviderMock(['sendMessage']);
        $emailProvider->expects($this->once())->method('sendMessage');

        $emailProvider->registrationWithTemporaryPassword($user, 'testpass');
    }

    /**
     * @test
     */
    public function it_sends_registration_mail()
    {
        $user = $this->getUserMock();
        $user->expects($this->exactly(2))->method('getEmail')->willReturn('user@example.com');

        $emailProvider = $this->getEmailProviderMock(['sendMessage']);
        $emailProvider->expects($this->once())->method('sendMessage');

        $emailProvider->registration($user, 'http://url.test');
    }

    /**
     * @test
     */
    public function it_sends_password_reset_email()
    {
        $user = $this->getUserMock();
        $user->expects($this->atLeastOnce())->method('getEmail')->willReturn('user@example.com');
        $user->expects($this->atLeastOnce())->method('getConfirmationToken')->willReturn('1234');

        $emailProvider = $this->getEmailProviderMock(['sendMessage']);
        $emailProvider->expects($this->once())->method('sendMessage');

        $emailProvider->resettingPasswordMessage($user);
    }

    /**
     * @test
     */
    public function it_sends_email_after_campaign_purchase()
    {
        $customerDetails = $this->getCustomerDetailsMock();
        $customerDetails->expects($this->once())->method('getEmail')->willReturn('user@example.com');

        $campaign = $this->getCampaignMock();
        $campaign->expects($this->once())->method('getName')->willReturn('Test reward');
        $campaign->expects($this->once())->method('getUsageInstruction')->willReturn('Instruction');

        $coupon = $this->getCouponMock();
        $coupon->expects($this->once())->method('getCode')->willReturn('1234');

        $emailProvider = $this->getEmailProviderMock(['sendMessage']);
        $emailProvider->expects($this->once())->method('sendMessage');

        $emailProvider->customerBoughtCampaign($customerDetails, $campaign, $coupon);
    }

    /**
     * @test
     */
    public function it_sends_add_points_to_customer_email()
    {
        $customerDetails = $this->getCustomerDetailsMock();
        $customerDetails->expects($this->once())->method('getEmail')->willReturn('user@example.com');

        $emailProvider = $this->getEmailProviderMock(['sendMessage']);
        $emailProvider->expects($this->once())->method('sendMessage');

        $emailProvider->addPointsToCustomer($customerDetails, 112, 12);
    }

    /**
     * @test
     */
    public function it_sends_move_to_level_email()
    {
        $customerDetails = $this->getCustomerDetailsMock();
        $customerDetails->expects($this->once())->method('getEmail')->willReturn('user@example.com');

        $reward = $this->getRewardMock();
        $reward->expects($this->once())->method('getValue')->willReturn(0.3);

        $level = $this->getLevelMock();
        $level->expects($this->once())->method('getName')->willReturn('New level');
        $level->expects($this->once())->method('getReward')->willReturn($reward);

        $emailProvider = $this->getEmailProviderMock(['sendMessage']);
        $emailProvider->expects($this->once())->method('sendMessage');

        $emailProvider->moveToLevel($customerDetails, $level);
    }

    /**
     * @param array|null $methods
     * @param array|null $methodsExcept
     *
     * @return PHPUnit_Framework_MockObject_MockObject|EmailProvider
     */
    public function getEmailProviderMock(array $methods = null, array $methodsExcept = null)
    {
        /** @var PHPUnit_Framework_MockObject_MockBuilder $emailProvider */
        $emailProvider = $this->getMockBuilder(EmailProvider::class)->setConstructorArgs(
            [
                $this->messageFactory,
                $this->mailer,
                $this->parameters,
            ]
        );

        if (!empty($methods)) {
            $emailProvider->setMethods($methods);
        }
        if (!empty($methodsExcept)) {
            $emailProvider->setMethodsExcept($methodsExcept);
        }

        return $emailProvider->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|CustomerDetails
     */
    public function getCustomerDetailsMock()
    {
        return $this->getMockBuilder(CustomerDetails::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|User
     */
    public function getUserMock()
    {
        return $this->getMockBuilder(User::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Campaign
     */
    public function getCampaignMock()
    {
        return $this->getMockBuilder(Campaign::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Coupon
     */
    public function getCouponMock()
    {
        return $this->getMockBuilder(Coupon::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Level
     */
    public function getLevelMock()
    {
        return $this->getMockBuilder(Level::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Reward
     */
    public function getRewardMock()
    {
        return $this->getMockBuilder(Reward::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
