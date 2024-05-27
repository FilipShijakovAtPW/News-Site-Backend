<?php

declare(strict_types=1);

namespace App\Controller;

use App\Serialization\NormalizationGroups;
use App\Service\Interface\ArticleServiceInterface;
use App\Service\QueryParameterExtractorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("/api/article")]
class ArticleController extends AbstractController
{
    public function __construct(
        private QueryParameterExtractorService $parameterExtractorService,
        private ArticleServiceInterface $articleService,
        private SerializerInterface $serializer
    )
    {
    }

    #[Route("/", name: "published_articles", methods: ["GET"])]
    public function publishedArticles(Request $request): Response
    {
        $parameters = $this->parameterExtractorService->extractQueryParameters($request);

        $items = $this->articleService->getPublishedArticles($parameters);

        return JsonResponse::fromJsonString(
            $this->serializer->serialize($items, 'json', ['groups' => [NormalizationGroups::PUBLISHED_ARTICLES]])
        );
    }
}
