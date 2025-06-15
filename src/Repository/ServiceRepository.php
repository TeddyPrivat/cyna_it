<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    /**
     * Save a Service entity.
     *
     * @param Service $service
     * @param bool $flush Whether to flush the changes immediately
     */
    public function save(Service $service, bool $flush = false): void
    {
        $this->getEntityManager()->persist($service);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a Service entity.
     *
     * @param Service $service
     * @param bool $flush Whether to flush the changes immediately
     */
    public function remove(Service $service, bool $flush = false): void
    {
        $this->getEntityManager()->remove($service);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
