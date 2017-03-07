<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 01.02.17 14:33
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenLoyalty\Domain\Email\Command;

use OpenLoyalty\Domain\Email\EmailId;

/**
 * Class UpdateEmail.
 */
final class UpdateEmail extends EmailCommand
{
    /**
     * Email data.
     * 
     * @var array
     */
    private $data;

    /**
     * {@inheritdoc}
     *
     * @param array $data
     */
    public function __construct(EmailId $emailId, array $data)
    {
        parent::__construct($emailId);

        $this->validateCommand($data);

        $this->data = $data;
    }

    /**
     * Get email data.
     *
     * @return array
     */
    public function getEmailData()
    {
        return $this->data;
    }
}
