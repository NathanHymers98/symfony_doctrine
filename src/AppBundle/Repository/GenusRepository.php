<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityRepository;

class GenusRepository extends EntityRepository
{
    /**
     * @return Genus[]
     */
    public function findAllPublishedOrderedBySize() // Since we are creating this new query inside the GenusRepo
                                                    // It knows to start every query with SELECT * FROM genus.
    {
        return $this->createQueryBuilder('genus') // This returns a queryBuilder object. The argument is an alias that gets added to the query.
                                                        // In this case SELECT * FROM genus as genus
            ->andWhere('genus.isPublished = :isPublished') // where the genus property is equal to the isPublished parameter
            ->setParameter('isPublished', true) // Setting the isPublished parameter to true. This is to stop SQL injection attacks
            ->orderBy('genus.speciesCount', 'DESC') // Ordering the results by the species count, in descending order.
            ->getQuery() // Since the query is complete, now I call getQuery() and execute() to execute the query when this function is called
            ->execute();
    }

}