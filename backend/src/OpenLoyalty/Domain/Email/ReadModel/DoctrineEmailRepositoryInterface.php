<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 03.02.17 10:10
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
