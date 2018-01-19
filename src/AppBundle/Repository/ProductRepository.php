<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ProductRepository extends EntityRepository
{
    public function sortProductsFromCategory($categoryName)
    {
//        $em = $this->getEntityManager();
//        $connection = $em->getConnection();
//        $statement = $connection->prepare("SELECT prod.name
//FROM product AS prod
//INNER JOIN category_product AS cp
//ON prod.id = cp.product_id
//INNER JOIN category AS cat
//ON cp.category_id = cat.id
//WHERE cat.slug = :categoryName");
//        $statement->bindValue('categoryName', $categoryName);
//        $em->createQuery($statement);

        return $this->createQueryBuilder('catProd')
            ->innerJoin('catProd.relationProductsCategories', 'catRelation')
            ->innerJoin('catRelation.category', 'cat')
            ->andWhere('cat.slug = :categoryName')
            ->setParameter('categoryName', $categoryName)
            ->orderBy('catRelation.position', 'ASC')
            ->getQuery()
            ->execute();
    }
}