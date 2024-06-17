<?php

declare(strict_types=1);

namespace App\Controller;

use App\Authorization\ArticleVoter;
use App\Commanding\Commands\ChangePublishedStateForArticleCommand;
use App\Commanding\Commands\CreateArticleCommand;
use App\Commanding\Commands\EditArticleCommand;
use App\Deserialization\ControllerTraits\WorksWithFractalTrait;
use App\Deserialization\ControllerTraits\WorksWithJsonDecoderTrait;
use App\Deserialization\ControllerTraits\WorksWithQueryExtractorTrait;
use App\Entity\User;
use App\Model\ArticlesRepositoryInterface;
use App\Model\Identifier\Identifier;
use App\Transofmers\ArticleTransformer;
use App\Validation\ControllerTraits\WorksWithValidationTrait;
use App\Validation\JsonValidators\CreateArticleJsonValidator;
use App\Validation\JsonValidators\EditArticleJsonValidator;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api")]
class ArticleController extends AbstractController
{
    use WorksWithValidationTrait;
    use WorksWithJsonDecoderTrait;
    use WorksWithFractalTrait;
    use WorksWithQueryExtractorTrait;

    public function __construct(
        private SerializerInterface            $serializer,
        private ValidatorInterface             $validator,
        private ArticlesRepositoryInterface    $articlesRepository,
    )
    {
    }

    #[Route('/dashboard/article', name: 'dashboard-get-articles', methods: ['GET'])]
    public function getArticles(
        Request                     $request,
        ArticleTransformer          $articleTransformer
    ): Response
    {
        $isEditor = $this->isGranted(User::ROLE_EDITOR);

        $parameters = $this->extractQueryParameters($request);

        if ($isEditor) {
            $items = $this->articlesRepository->getAllArticlesWithFilters($parameters);
            $excludes = [];
        } else {
            $items = $this->articlesRepository->getPublishedArticlesWithFilters($parameters);
            $excludes = ['isPublished'];
        }

        $resource = new Collection($items, $articleTransformer);

        return $this->createJsonResponse($resource, Response::HTTP_OK, [], $excludes);
    }

    #[Route("/dashboard/user-article", name: "dashboard-user-articles", methods: ["GET"])]
    public function userArticles(Request $request, ArticleTransformer $articleTransformer): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_WRITER);

        $parameters = $this->extractQueryParameters($request);

        $items = $this->articlesRepository->getUserArticlesWithFilters($this->getUser(), $parameters);

        return $this->createJsonResponse(new Collection($items, $articleTransformer));
    }

    #[Route('/dashboard/article/{id}/change-published-state', name: 'dashboard-change-published-state-article', methods: ['GET'])]
    public function changePublishedStateForArticle(string $id, CommandBus $bus): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_EDITOR);

        $bus->handle(new ChangePublishedStateForArticleCommand(Identifier::fromString($id)));

        return new Response();
    }

    #[Route('/dashboard/article', name: 'dashboard-create-article', methods: ['POST'])]
    public function createArticle(
        Request                    $request,
        CreateArticleJsonValidator $validator,
        CommandBus                 $bus,
        ArticleTransformer $articleTransformer
    ): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_WRITER);

        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        /** @var Identifier $articleId */
        $articleId = $this->articlesRepository->getNextIdentifier();

        $bus->handle(new CreateArticleCommand(
            $articleId,
            $this->getUser(),
            $data['title'],
            $data['summary'],
            $data['content']
        ));

        $article = $this->articlesRepository->getArticleById($articleId);

        return $this->createJsonResponse(
            new Item($article, $articleTransformer),
            Response::HTTP_CREATED
        );
    }

    #[Route('/dashboard/article/{id}', name: 'dashboard-edit-article', methods: ['PUT'])]
    public function editArticle(
        string                   $id,
        Request                  $request,
        EditArticleJsonValidator $validator,
        CommandBus               $bus,
        ArticleTransformer $articleTransformer
    ): Response
    {
        $this->denyAccessUnlessGranted(ArticleVoter::EDIT, $id);

        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        $identifier = Identifier::fromString($id);

        $bus->handle(new EditArticleCommand(
            $identifier,
            $data['title'] ?? null,
            $data['summary'] ?? null,
            $data['content'] ?? null
        ));

        $article = $this->articlesRepository->getArticleById($identifier);

        return $this->createJsonResponse(
            new Item($article, $articleTransformer),
            Response::HTTP_CREATED
        );
    }
}
