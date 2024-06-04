<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class QueryParameterExtractorService
{
    const ARTICLE_PROPERTIES = ['title', 'summary', 'content', 'published'];

    public function extractQueryParameters(Request $request): array
    {
        $pageNumber = $request->query->getInt('pageNumber', 1);
        $pageSize = $request->query->getInt('pageSize', 10);
        $orderByAsc = $request->query->get('orderByAsc');
        $orderByDesc = $request->query->get('orderByDesc');
        $matches = $request->query->get('matches');
        $contains = $request->query->get('contains');

        if (!in_array($orderByAsc, self::ARTICLE_PROPERTIES)) {
            $orderByAsc = null;
        }
        if (!in_array($orderByDesc, self::ARTICLE_PROPERTIES)) {
            $orderByDesc = null;
        }

        return [
            'pageNumber' => $pageNumber,
            'pageSize' => $pageSize,
            'orderByAsc' => $orderByAsc,
            'orderByDesc' => $orderByDesc,
            'matches' => $matches,
            'contains' => $contains
        ];
    }
}