<?php

namespace OpenLoyaltyPlugin\SalesManagoBundle\Service;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class SalesManagoTranslator.
 */
class SalesManagoCustomerTranslator
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * SalesManagoCustomerTranslator constructor.
     *
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function translateToSalesManago($data)
    {
        foreach ($data['properties'] as $key => $value) {
            $data['properties'][$this->translator->trans($key)] = $value;
            unset($data['properties'][$key]);
        }

        return $data;
    }
}
