<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Email\ReadModel;

use OpenLoyalty\Domain\Email\EmailId;

/**
 * Interface DoctrineEmailRepositoryInterface.
 */
interface DoctrineEmailRepositoryInterface
{
    /**
     * @return array
     */
    public function getAll();

    /**
     * @param EmailId $emailId
     *
     * @return Email
     */
    public function getById(EmailId $emailId);

    /**
     * @param $key
     *
     * @return Email
     */
    public function getByKey($key);
}
