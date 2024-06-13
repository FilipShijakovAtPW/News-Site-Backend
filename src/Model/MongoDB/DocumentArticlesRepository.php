<?php

namespace App\Model\MongoDB;

use App\Document\Article;
use App\Document\User;
use App\Model\ArticlesRepositoryInterface;
use App\Model\Identifier\Identifier;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class DocumentArticlesRepository implements ArticlesRepositoryInterface
{
    public function __construct(private DocumentManager $documentManager)
    {
    }

    private function getRepository(): DocumentRepository
    {
        return $this->documentManager->getRepository(Article::class);
    }

    private function getQueryBuilderWithFilters($filters): Builder
    {
        $pageSize = $filters['pageSize'];
        $pageStart = ($filters['pageNumber'] - 1) * $pageSize;

        $qb = $this->documentManager->createQueryBuilder(Article::class);


        if ($filters['matches'])
        {
            $qb->field('title')->equals($filters['matches']);
        }
        else if ($filters['contains'])
        {
            $contains = 'a';
            // TODO
            $qb->field('title')->equals(`/.*$contains.*/`);
        }

        if ($filters['orderByAsc'])
        {
            $qb->sort($filters['orderByAsc'], 'asc');
        }
        else if ($filters['orderByDesc'])
        {
            $qb->sort($filters['orderByDesc'], 'desc');
        }

        return $qb->skip($pageStart)
            ->limit($pageSize);
    }

    public function getNextIdentifier()
    {
        return Identifier::generate();
    }

    public function getArticleById(Identifier $identifier)
    {
        return $this->getRepository()->find($identifier->getId());
    }

    public function getPublishedArticlesWithFilters(array $filters)
    {
        return $this->getQueryBuilderWithFilters($filters)
            ->field('isPublished')->equals('true')
            ->getQuery()
            ->execute();
    }

    public function getAllArticlesWithFilters(array $filters)
    {
        return $this->getQueryBuilderWithFilters($filters)
            ->getQuery()
            ->execute();
    }

    public function getUserArticlesWithFilters(User $user, array $filters)
    {
        return $this->getQueryBuilderWithFilters($filters)
            ->field('user')->equals($user->getId())
            ->getQuery()
            ->execute();
    }

    public function saveArticle(Article $article)
    {
        $this->documentManager->persist($article);
        $this->documentManager->flush();
    }

    public function flush()
    {
        $this->documentManager->flush();
    }
}