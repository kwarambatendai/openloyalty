<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\TransactionBundle\Form\Type;

use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TransactionIdFormType.
 */
class TransactionIdFormType extends AbstractType
{
    /**
     * @var TransactionDetailsRepository
     */
    protected $transactionDetailsRepository;

    /**
     * TransactionChoiceFormType constructor.
     *
     * @param TransactionDetailsRepository $transactionDetailsRepository
     */
    public function __construct(TransactionDetailsRepository $transactionDetailsRepository)
    {
        $this->transactionDetailsRepository = $transactionDetailsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $validateCustomerIsNull = $options['validate_customer_is_null'];
        $repo = $this->transactionDetailsRepository;
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($repo, $validateCustomerIsNull) {
            $data = $event->getData();
            $transaction = $repo->find($data);
            if (!$transaction instanceof TransactionDetails) {
                $event->getForm()->addError(new FormError('Transaction does not exist'));
            }

            if ($validateCustomerIsNull && $transaction->getCustomerId()) {
                $event->getForm()->addError(new FormError('Customer is already assign to this transaction'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validate_customer_is_null' => null,
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
