<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\SettingsBundle\Model;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use OpenLoyalty\Bundle\SettingsBundle\Validator\Constraints as AppAssert;

/**
 * Class TranslationsEntry.
 *
 * @AppAssert\NotUsedKey(groups={"edit"})
 * @AppAssert\UniqueKey()
 */
class TranslationsEntry
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     * @JMS\Exclude()
     */
    private $previousKey;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @AppAssert\ValidJson()
     */
    private $content = null;

    /**
     * @var \DateTime
     */
    private $updatedAt = null;

    public function __construct($key = null, $content = null, \DateTime $updatedAt = null)
    {
        $this->key = $key;
        $this->previousKey = $key;
        $this->content = $content;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     * @JMS\VirtualProperty()
     */
    public function getName()
    {
        return str_replace('.json', '', $this->key);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->key = $name.'.json';
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getPreviousKey()
    {
        return $this->previousKey;
    }

    /**
     * @param string $previousKey
     */
    public function setPreviousKey($previousKey)
    {
        $this->previousKey = $previousKey;
    }
}
