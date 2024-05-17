<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 *
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointment[]    findAll()
 * @method Appointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    public function filterByYearAndMonth($year, $month)
    {
        $qb = $this->createQueryBuilder('appointment');
        $qb
            ->andWhere($qb->expr()->eq('year(appointment.startedAt)', ':year'))
            ->andWhere($qb->expr()->eq('month(appointment.startedAt)', ':month'))
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->orderBy('appointment.startedAt', 'ASC');
        return $qb->getQuery()->getResult();
    }
}
