<?php

namespace App\Repository;

use App\Entity\Actor;
use App\Entity\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Program>
 *
 * @method Program|null find($id, $lockMode = null, $lockVersion = null)
 * @method Program|null findOneBy(array $criteria, array $orderBy = null)
 * @method Program[]    findAll()
 * @method Program[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }

    public function findLikeName(string $name)
    {
        $queryBuilder = $this->createQueryBuilder('program')
            ->join('program.actors', 'actor')
            ->where('program.title LIKE :name')
            ->orWhere('actor.name LIKE :nameActor')
            ->setParameter('name' , '%'. $name . '%')
            ->setParameter('nameActor', '%' . $name. '%')
            ->orderBy('program.title', 'ASC')
            ->getQuery();

        return $queryBuilder->getResult();
    }

    public function findRecentPrograms()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT program, seson FROM App\Entity\Program program
                                INNER JOIN program.seasons season
                                WHERE season>years>2010');
                            
        return $query->execute();

    }
}
