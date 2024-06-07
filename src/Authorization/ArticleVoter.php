<?php

namespace App\Authorization;

use App\Entity\Article;
use App\Entity\User;
use App\Model\ArticlesRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ArticleVoter extends Voter
{
    public const EDIT = "EDIT_ARTICLE";

    public function __construct(private Security $security, private ArticlesRepositoryInterface $articlesRepository)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute == self::EDIT;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($attribute === self::EDIT) {
            /** @var Article $article */
            $article = $this->articlesRepository->getArticleById($subject);

            if ($this->security->isGranted(User::ROLE_WRITER) &&
                $article->getUser()->getUserIdentifier() === $user->getUserIdentifier()) {
                return true;
            }
        }

        return false;
    }
}