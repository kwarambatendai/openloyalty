<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\CampaignBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use OpenLoyalty\Bundle\CampaignBundle\Validator\Constraints as CampaignAssert;
use OpenLoyalty\Domain\Campaign\Model\CampaignPhoto as DomainCampaignPhoto;

/**
 * Class CampaignPhoto.
 */
class CampaignPhoto extends DomainCampaignPhoto
{
    /**
     * @var UploadedFile
     * @Assert\NotBlank()
     * @CampaignAssert\Image(
     *     mimeTypes={"image/png", "image/gif", "image/jpeg"},
     *     maxSize="2M"
     * )
     */
    protected $file;

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
}
