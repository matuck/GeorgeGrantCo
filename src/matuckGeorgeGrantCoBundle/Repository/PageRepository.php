<?php

namespace matuckGeorgeGrantCoBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PageRepository extends EntityRepository
{
    public function getHomePage()
    {
        $query = $this->createQueryBuilder('p');
        $query->where('p.home = :home');
        $query->setParameter('home', true);

        return $query->getQuery()->getSingleResult();
    }
}
