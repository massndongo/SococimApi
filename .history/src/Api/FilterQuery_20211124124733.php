<?php

namespace App\Api;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Carte;
use App\Entity\Checkpoint;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

class FilterQuery implements QueryCollectionExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($resourceClass === User::class || $resourceClass === Checkpoint::class)
        {
            $queryBuilder->andWhere(sprintf("%s.blocked = false",
            $queryBuilder->getRootAliases()[0]));
        }
    }
}
