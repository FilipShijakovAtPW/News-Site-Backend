<?php

declare(strict_types=1);

namespace App\Controller;

use App\Authorization\ArticleVoter;
use App\Commanding\Commands\ChangePublishedStateForArticleCommand;
use App\Commanding\Commands\CreateArticleCommand;
use App\Commanding\Commands\EditArticleCommand;
use App\Deserialization\ControllerTraits\WorksWithJsonDecoderTrait;
use App\Entity\Article;
use App\Entity\User;
use App\Model\ArticlesRepositoryInterface;
use App\Serialization\NormalizationGroups;
use App\Service\Interface\ArticleServiceInterface;
use App\Service\QueryParameterExtractorService;
use App\Service\SerializationAndValidationService;
use App\Validation\ControllerTraits\WorksWithValidationTrait;
use App\Validation\JsonValidators\CreateArticleJsonValidator;
use App\Validation\JsonValidators\EditArticleJsonValidator;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    public function __construct(
        private QueryParameterExtractorService    $parameterExtractorService,
        private ArticleServiceInterface           $articleService,
        private SerializerInterface               $serializer,
        private SerializationAndValidationService $serializationAndValidationService,
        private ValidatorInterface                $validator,
        private ArticlesRepositoryInterface $articlesRepository,
    )
    {
    }

    #[Route('/dashboard/article', name: 'dashboard-get-articles', methods: ['GET'])]
    public function getArticles(Request $request): Response
    {
        $isEditor = $this->isGranted(User::ROLE_EDITOR);

        $parameters = $this->parameterExtractorService->extractQueryParameters($request);

        if ($isEditor) {
            $items = $this->articleService->getAllArticles($parameters);
        } else {
            $items = $this->articleService->getPublishedArticles($parameters);
        }

        return JsonResponse::fromJsonString(
            $this->serializer->serialize($items, 'json', ['groups' => [NormalizationGroups::ALL_ARTICLES]]));
    }

    #[Route("/dashboard/user-article", name: "dashboard-user-articles", methods: ["GET"])]
    public function userArticles(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_WRITER);

        $parameters = $this->parameterExtractorService->extractQueryParameters($request);

        $items = $this->articleService->getUserArticles($this->getUser(), $parameters);

        return JsonResponse::fromJsonString(
            $this->serializer->serialize($items, 'json', ['groups' => [NormalizationGroups::ALL_ARTICLES]])
        );
    }

    #[Route('/dashboard/article/{id}/change-published-state', name: 'dashboard-change-published-state-article', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function changePublishedStateForArticle(int $id, CommandBus $bus): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_EDITOR);

        $bus->handle(new ChangePublishedStateForArticleCommand($id));

        return new Response();
    }

    #[Route('/dashboard/article', name: 'dashboard-create-article', methods: ['POST'])]
    public function createArticle(Request $request, CreateArticleJsonValidator $validator, CommandBus $bus): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_WRITER);

        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        $bus->handle(new CreateArticleCommand(
            $this->getUser(),
            $data['title'],
            $data['summary'],
            $data['content']
        ));

        return JsonResponse::fromJsonString("", Response::HTTP_CREATED);
    }

    #[Route('/dashboard/article/{id}', name: 'dashboard-edit-article', methods: ['PUT'])]
    public function editArticle(int $id, Request $request, EditArticleJsonValidator $validator, CommandBus $bus): Response
    {
        $this->denyAccessUnlessGranted(ArticleVoter::EDIT, $id);

        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        $bus->handle(new EditArticleCommand(
            $id,
            $data['title'] ?? null,
            $data['summary'] ?? null,
            $data['content'] ?? null
        ));

        return JsonResponse::fromJsonString(
            "",
            Response::HTTP_CREATED
        );
    }
}
