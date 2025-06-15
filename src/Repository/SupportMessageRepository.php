<?php

namespace App\Repository;

use App\Entity\SupportMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SupportMessage>
 */
class SupportMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SupportMessage::class);
    }

    /**
     * Save a SupportMessage entity.
     *
     * @param SupportMessage $sm
     * @param bool $flush Whether to flush the changes immediately
     */
    public function save(SupportMessage $sm, bool $flush = false): void
    {
        $this->getEntityManager()->persist($sm);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a Service entity.
     *
     * @param SupportMessage $sm
     * @param bool $flush Whether to flush the changes immediately
     */
    public function remove(SupportMessage $sm, bool $flush = false): void
    {
        $this->getEntityManager()->remove($sm);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
