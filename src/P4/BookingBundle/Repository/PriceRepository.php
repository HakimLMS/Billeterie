<?php

namespace P4\BookingBundle\Repository;

/**
 * PriceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PriceRepository extends \Doctrine\ORM\EntityRepository
{
    public function findPrice($type)
    {
        
        $qb = $this->createQueryBuilder('p')//prendre première ligne
                ->select('p.amount')
                ->where('p.type = :type')
                ->setParameter(':type',$type);
        
        $result = $qb->getQuery()
                ->getSingleResult();
        
        return $result['amount'];
    }
}
