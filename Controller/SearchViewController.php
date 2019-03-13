<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\HulkBundle\Service\SearchViewProvider;

class SearchViewController extends AbstractController
{

    private $searchView;

    private $requestStack;

    public function __construct(SearchViewProvider $searchView, RequestStack $requestStack)
    {
        $this->searchView = $searchView;
        $this->requestStack = $requestStack;
    }

    /**
     * @throws \Exception
     */
    public function searchView()
    {
        $params = $this->requestStack->getCurrentRequest()->request->all();
        $this->checkRequestParams($params);

        try {
            $results = $this->searchView->getBoomResults($params['entity'], $params['columns'], $params['search']);
        } catch (BadRequestHttpException $e) {
            return new JsonResponse($e->getMessage(), 400);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse('Calculation view isn\'t in service.xsodata file', 400);
        }

        if (count($results) === 0){
            return new JsonResponse('', 204);
        }

        return $this->render('@W3comHulk/search_view/list.html.twig', ['results' => $results]);
    }

    private function checkRequestParams(array $params)
    {
        if (!array_key_exists('entity', $params)){
            return new JsonResponse('Missing entity parameter', 400);
        } elseif (!array_key_exists('columns', $params)){
            return new JsonResponse('Missing columns parameter', 400);
        }
    }
}