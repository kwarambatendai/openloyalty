<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\SettingsBundle\Form\DataTransformer;

use OpenLoyalty\Bundle\SettingsBundle\Entity\StringSettingEntry;
use OpenLoyalty\Bundle\SettingsBundle\Service\SettingsManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class StringSettingDataTransformer.
 */
class StringSettingDataTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * StringSettingDataTransformer constructor.
     *
     * @param string          $key
     * @param SettingsManager $settingsManager
     */
    public function __construct($key, SettingsManager $settingsManager)
    {
        $this->key = $key;
        $this->settingsManager = $settingsManager;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        if ($value == null) {
            return;
        }
        if (!$value instanceof StringSettingEntry) {
            throw new \InvalidArgumentException();
        }

        return $value->getValue();
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return;
        }

        $entry = $this->settingsManager->getSettingByKey($this->key);
        if (!$entry) {
            $entry = new StringSettingEntry($this->key);
        }
        $entry->setValue($value);

        return $entry;
    }
}
