<?php

declare(strict_types=1);

namespace App\Controller;

use App\Deserialization\DenormalizationGroups;
use App\Entity\Article;
use App\Entity\User;
use App\Exception\ArticleNotFoundException;
use App\Exception\UserCantEditOthersArticleException;
use App\Serialization\NormalizationGroups;
use App\Service\Interface\ArticleServiceInterface;
use App\Service\QueryParameterExtractorService;
use App\Service\SerializationAndValidationService;
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
    public function __construct(
        private QueryParameterExtractorService $parameterExtractorService,
        private ArticleServiceInterface $articleService,
        private SerializerInterface $serializer,
        private SerializationAndValidationService $serializationAndValidationService,
        private ValidatorInterface $validator
    )
    {
    }

    #[Route("/article", name: "published-articles", methods: ["GET"])]
    public function publishedArticles(Request $request): Response
    {
        $parameters = $this->parameterExtractorService->extractQueryParameters($request);

        $items = $this->articleService->getPublishedArticles($parameters);

        return JsonResponse::fromJsonString(
            $this->serializer->serialize($items, 'json', ['groups' => [NormalizationGroups::PUBLISHED_ARTICLES]])
        );
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

    #[Route('/dashboard/article', name: 'dashboard-get-all-articles', methods: ['GET'])]
    public function getAllArticles(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_EDITOR);

        $parameters = $this->parameterExtractorService->extractQueryParameters($request);

        $items = $this->articleService->getAllArticles($parameters);

        return JsonResponse::fromJsonString($this->serializer->serialize($items, 'json', ['groups' => NormalizationGroups::ALL_ARTICLES]));
    }

    #[Route('/dashboard/article/{id}/change-published-state', name: 'dashboard-change-published-state-article', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function changePublishedStateForArticle(int $id): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_EDITOR);

        $this->articleService->changePublishedStateForArticle($id);

        return new Response();
    }

    #[Route('/dashboard/article', name: 'dashboard-create-article', methods: ['POST'])]
    public function createArticle(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_WRITER);

        $result = $this->serializationAndValidationService->serializeAndValidate(
            $request, $this->serializer, $this->validator, Article::class, [DenormalizationGroups::CREATE_ARTICLE]
        );

        if ($result instanceof Response) {
            return $result;
        }

        $user = $this->getUser();

        $result->setUser($user);

        try {
            $article = $this->articleService->createArticle($result);
            $returnVal = $this->serializer->serialize($article, 'json', ['groups' => [NormalizationGroups::ALL_ARTICLES]]);
            return JsonResponse::fromJsonString($returnVal, Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/dashboard/article/{id}', name: 'dashboard-edit-article', methods: ['PUT'])]
    public function editArticle(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_WRITER);

        $result = $this->serializationAndValidationService->serializeAndValidate(
            $request, $this->serializer, $this->validator, Article::class, null
        );

        if ($result instanceof Response) {
            return $result;
        }

        try
        {
            $editedArticle = $this->articleService->editArticle($id, $this->getUser(), $result);
        }
        catch (UserCantEditOthersArticleException $exception)
        {
            return new JsonResponse([
                'error' => $exception->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }
        catch (ArticleNotFoundException $exception)
        {
            return new JsonResponse([
                'error' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        return JsonResponse::fromJsonString(
            $this->serializer->serialize($editedArticle, 'json', ['groups' => [NormalizationGroups::ALL_ARTICLES]]),
            Response::HTTP_CREATED
        );
    }
}
