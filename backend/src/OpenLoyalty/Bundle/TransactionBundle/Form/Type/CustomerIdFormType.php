<?php

namespace OpenLoyalty\Bundle\TransactionBundle\Form\Type;

use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class CustomerIdFormType.
 */
class CustomerIdFormType extends AbstractType
{
    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;

    /**
     * CustomerIdFormType constructor.
     *
     * @param CustomerDetailsRepository $customerDetailsRepository
     */
    public function __construct(CustomerDetailsRepository $customerDetailsRepository)
    {
        $this->customerDetailsRepository = $customerDetailsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repo = $this->customerDetailsRepository;
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($repo) {
            $data = $event->getData();
            $customer = $repo->find($data);
            if (!$customer instanceof CustomerDetails) {
                $event->getForm()->addError(new FormError('Customer does not exist'));
            }
        });
    }

    public function getParent()
    {
        return TextType::class;
    }
}
