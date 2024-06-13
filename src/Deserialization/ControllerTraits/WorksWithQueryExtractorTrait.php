<?php

namespace App\Deserialization\ControllerTraits;

use Symfony\Component\HttpFoundation\Request;

trait WorksWithQueryExtractorTrait
{
    public function extractQueryParameters(Request $request)
    {
        $articleProperties = ['title', 'summary', 'content', 'published'];

        $pageNumber = $request->query->getInt('pageNumber', 1);
        $pageSize = $request->query->getInt('pageSize', 10);
        $orderByAsc = $request->query->get('orderByAsc');
        $orderByDesc = $request->query->get('orderByDesc');
        $matches = $request->query->get('matches');
        $contains = $request->query->get('contains');

        if (!in_array($orderByAsc, $articleProperties)) {
            $orderByAsc = null;
        }
        if (!in_array($orderByDesc, $articleProperties)) {
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