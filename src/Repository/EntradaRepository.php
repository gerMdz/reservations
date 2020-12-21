<?php

namespace App\Repository;

use App\Entity\Entrada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;


/**
 * @method Entrada|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entrada|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entrada[]    findAll()
 * @method Entrada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntradaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entrada::class);
    }

    // /**
    //  * @return Entrada[] Returns an array of Entrada objects
    //  */

    /**
     * @param $user
     * @return mixed
     */
    public function findByAutor($user)
    {
        return $this->queryFindByAutor($user)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @param $user
     * @return int|mixed|string
     */
    public function queryFindByAutor($user)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.autor = :val')
            ->setParameter('val', $user)
            ->orderBy('e.createdAt', 'DESC')
        ;
    }

    /**
     * @param null $user
     *
     * @return Entrada[]
     * @throws QueryException
     */
    public function findAllPublicadosOrderedByPublicacion($user = null)
    {
        $this->createQueryBuilder('e')
            ->addCriteria(self::createNoDeletedCriteria());

        return $this->addIsPublishedQueryBuilder(null, $user)
            ->orderBy('e.publicadoAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     *
     * @param $seccion
     * @return Entrada[]
     * @throws QueryException
     */
    public function findAllEntradasBySeccion($seccion): array
    {
        $this->createQueryBuilder('e')
            ->addCriteria(self::createDisponibleForDisponibleAt());

        return $this->getOrCreateQueryBuilder(null)
            ->leftJoin('e.sections','s' )
            ->orderBy('e.orden', 'ASC')
            ->andWhere(
                '(e.disponibleAt <= :today AND e.disponibleHastaAt >= :today)
                OR
                (e.isPermanente = true)'
            )
            ->andWhere('s.id = :section')
            ->setParameter('today',new DateTime('now'))
            ->setParameter('section', $seccion)
            ->getQuery()
            ->getResult()
            ;
    }

    public static function createNoDeletedCriteria():Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('isDeleted', false))
            ->orderBy(['createdAt'=>'DESC'])
        ;
    }
    
    public static function createDisponibleForDisponibleAt():Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('disponible', true))
            ->orderBy(['disponibleAt'=>'ASC'])
        ;
    }


    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    private function addIsPublishedQueryBuilder(QueryBuilder $qb = null, $user = null)
    {
        $qb = $this->getOrCreateQueryBuilder($qb)
            ->andWhere('e.publicadoAt IS NOT NULL');
        if (null != $user) {
            $qb->andWhere('e.autor = :val')
                ->setParameter('val', $user);
        }

        return $qb;
    }
    
    private function addIsDisponibleForSeccion(QueryBuilder $qb = null, $seccion)
    {
        $qb = $this->getOrCreateQueryBuilder($qb)
            ->andWhere('s.publicadoAt IS NOT NULL');


        return $qb;
    }

    private function getOrCreateQueryBuilder(QueryBuilder $qb = null)
    {
        return $qb ?: $this->createQueryBuilder('e');
    }

    /**
     * @return QueryBuilder
     */
    public function queryFindAllEntradas(): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.createdAt', 'DESC')
            ;
    }


}
