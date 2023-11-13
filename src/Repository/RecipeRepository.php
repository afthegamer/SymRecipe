<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findPublicRecipe(?int $nbRecipes) :array
    {
        sleep(3);//cette ligne est a des fin de test sur le cache a retirer pour une vrais mise en prod
        $queryBuilder = $this->createQueryBuilder('r')
            /**
             * il faut mettre
             * →where('r.isPublic = true')
             * si vous prévoyez de d'utiliser PostgreSQL ou un autre SGBD
             * sinon
             * →where('r.isPublic=1')
             * est valable à l'utilisation avec my sql par exemple
            */
            ->where('r.isPublic = true')
            ->orderBy('r.createdAt', 'DESC');
        if($nbRecipes !== 0 || $nbRecipes !== null)
        {
            $queryBuilder->setMaxResults($nbRecipes);
        }
        return $queryBuilder->getQuery()
            ->getResult();
    }
}
