<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 02.02.17 10:51
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenLoyalty\Infrastructure\Email\Persistance\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\Email\Email;
use OpenLoyalty\Domain\Email\EmailId;
use OpenLoyalty\Domain\Email\EmailRepositoryInterface;

/**
 * Class DoctrineEmailRepository.
 */
class DoctrineEmailRepository extends EntityRepository implements EmailRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getById(EmailId $emailId)
    {
        return $this->find($emailId->__toString());
    }

    /**
     * {@inheritdoc}
     */
    public function getByKey($key)
    {
        return $this->findOneBy(['key' => $key]);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Email $email)
    {
        $this->getEntityManager()->persist($email);
        $this->getEntityManager()->flush();
    }
}
