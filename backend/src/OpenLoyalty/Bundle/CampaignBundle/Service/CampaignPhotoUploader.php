<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\CampaignBundle\Service;

use Gaufrette\Filesystem;
use OpenLoyalty\Domain\Campaign\Model\CampaignPhoto;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class CampaignPhotoUploader.
 */
class CampaignPhotoUploader
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * FileUploader constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param CampaignPhoto $photo
     *
     * @return string|null
     */
    public function get(CampaignPhoto $photo)
    {
        if (null === $photo || null === $photo->getPath()) {
            return;
        }

        return $this->filesystem->get($photo->getPath())->getContent();
    }

    public function upload(UploadedFile $src)
    {
        $file = new CampaignPhoto();
        $fileName = md5(uniqid()).'.'.$src->guessExtension();
        $file->setPath('campaign_photos'.DIRECTORY_SEPARATOR.$fileName);
        $file->setMime($src->getClientMimeType());
        $file->setOriginalName($src->getClientOriginalName());

        $this->filesystem->write($file->getPath(), file_get_contents($src->getRealPath()));
        unlink($src->getRealPath());

        return $file;
    }

    public function remove(CampaignPhoto $file = null)
    {
        if (null === $file || null === $file->getPath()) {
            return;
        }

        $path = $file->getPath();
        if ($this->filesystem->has($path)) {
            $this->filesystem->delete($path);
        }
    }
}
