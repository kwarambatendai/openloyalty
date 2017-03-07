<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

use OpenLoyalty\Bundle\EmailBundle\Mailer\OloyMailer;
use OpenLoyalty\Bundle\EmailBundle\Service\MessageFactoryInterface;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Customer\Model\Coupon;

/**
 * Class EmailProvider.
 */
class EmailProvider
{
    /**
     * @var MessageFactoryInterface
     */
    protected $messageFactory;

    /** @var array */
    protected $parameters = [];

    /** @var OloyMailer */
    protected $mailer;
    protected $emailFromName;
    protected $emailFromAddress;
    protected $passwordResetUrl;
    protected $loyaltyProgramName;
    protected $ecommerceAddress;
    protected $customerPanelUrl;

    /**
     * EmailProvider constructor.
     *
     * @param MessageFactoryInterface $messageFactory
     * @param OloyMailer              $mailer
     * @param array                   $parameters
     */
    public function __construct(MessageFactoryInterface $messageFactory, OloyMailer $mailer, array $parameters)
    {
        $this->messageFactory = $messageFactory;
        $this->mailer = $mailer;
        $this->parameters = $parameters;
        $this->emailFromName = isset($parameters['from_name']) ? $parameters['from_name'] : '';
        $this->emailFromAddress = isset($parameters['from_address']) ? $parameters['from_address'] : '';
        $this->passwordResetUrl = isset($parameters['password_reset_url']) ? $parameters['password_reset_url'] : '';
        $this->loyaltyProgramName = isset($parameters['loyalty_program_name']) ? $parameters['loyalty_program_name'] : '';
        $this->ecommerceAddress = isset($parameters['ecommerce_address']) ? $parameters['ecommerce_address'] : '';
        $this->customerPanelUrl = isset($parameters['customer_panel_url']) ? $parameters['customer_panel_url'] : '';
    }

    /**
     * @param string      $subject
     * @param string      $email
     * @param string|null $template
     * @param array|null  $params
     */
    public function sendMessage(string $subject, string $email, string $template = null, array $params = null)
    {
        $message = $this->messageFactory->create();
        $message->setSubject($subject);
        $message->setRecipientEmail($email);
        $message->setRecipientName($email);
        $message->setSenderEmail($this->emailFromAddress);
        $message->setSenderName($this->emailFromName);
        $message->setTemplate($template);
        $message->setParams($params);

        $this->mailer->send($message);
    }

    /**
     * @param CustomerDetails $registeredUser
     * @param string          $password
     */
    public function registrationWithTemporaryPassword(CustomerDetails $registeredUser, string $password)
    {
        $this->sendMessage(
            'Account created',
            $registeredUser->getEmail(),
            'OpenLoyaltyUserBundle:email:registration_with_temporary_password.html.twig',
            [
                'program_name' => $this->loyaltyProgramName,
                'email' => $registeredUser->getEmail(),
                'loyalty_card_number' => $registeredUser->getLoyaltyCardNumber(),
                'phone' => $registeredUser->getPhone(),
                'password' => $password,
                'customer_panel_url' => $this->customerPanelUrl,
            ]
        );
    }

    /**
     * @param User        $registeredUser
     * @param string|null $url
     */
    public function registration(User $registeredUser, string $url = null)
    {
        $this->sendMessage(
            'Account created',
            $registeredUser->getEmail(),
            'OpenLoyaltyUserBundle:email:registration.html.twig',
            [
                'username' => $registeredUser->getEmail(),
                'url' => $url,
            ]
        );
    }

    /**
     * @param User $user
     */
    public function resettingPasswordMessage(User $user)
    {
        $this->sendMessage(
            'Password reset requested',
            $user->getEmail(),
            'OpenLoyaltyUserBundle:email:password_reset.html.twig',
            [
                'program_name' => $this->loyaltyProgramName,
                'url_reset_password' => $this->passwordResetUrl.'/'.$user->getConfirmationToken(),
            ]
        );
    }

    /**
     * @param CustomerDetails $customer
     * @param Campaign        $campaign
     * @param Coupon          $coupon
     */
    public function customerBoughtCampaign(CustomerDetails $customer, Campaign $campaign, Coupon $coupon)
    {
        $subject = sprintf('%s - new reward', $this->loyaltyProgramName);

        $this->sendMessage(
            $subject,
            $customer->getEmail(),
            'OpenLoyaltyUserBundle:email:customer_reward_bought.html.twig',
            [
                'program_name' => $this->loyaltyProgramName,
                'reward_name' => $campaign->getName(),
                'reward_code' => $coupon->getCode(),
                'reward_instructions' => $campaign->getUsageInstruction(),
                'ecommerce_address' => $this->ecommerceAddress,
            ]
        );
    }

    /**
     * @param CustomerDetails $customer
     * @param int             $availableAmount
     * @param int             $pointsAdded
     */
    public function addPointsToCustomer(CustomerDetails $customer, int $availableAmount, int $pointsAdded)
    {
        $subject = sprintf('%s - new points', $this->loyaltyProgramName);

        $this->sendMessage(
            $subject,
            $customer->getEmail(),
            'OpenLoyaltyUserBundle:email:new_points.html.twig',
            [
                'program_name' => $this->loyaltyProgramName,
                'added_points_amount' => $pointsAdded,
                'active_points_amount' => $availableAmount,
                'ecommerce_address' => $this->ecommerceAddress,
            ]
        );
    }

    /**
     * @param CustomerDetails $customer
     * @param Level           $level
     */
    public function moveToLevel(CustomerDetails $customer, Level $level)
    {
        $subject = sprintf('%s - new level', $this->loyaltyProgramName);

        $this->sendMessage(
            $subject,
            $customer->getEmail(),
            'OpenLoyaltyUserBundle:email:new_level.html.twig', [
                'program_name' => $this->loyaltyProgramName,
                'level_name' => $level->getName(),
                'level_discount' => number_format($level->getReward()->getValue() * 100, 0),
                'ecommerce_address' => $this->ecommerceAddress,
            ]
        );
    }
}
