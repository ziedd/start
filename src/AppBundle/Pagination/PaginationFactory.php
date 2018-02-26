<?php
/**
 * Created by PhpStorm.
 * User: challouf
 * Date: 16/09/17
 * Time: 15:31
 */

namespace AppBundle\Pagination;


use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{

    private $route;
  
    public function __construct(RouterInterface $route)
    {
        
        $this->route =$route ;
    
    }
    


    public function createCollection(QueryBuilder $qb ,Request $request,$route, array $routeParams)
    {

        $page = $request->query->get('page',1);
        
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);
        $programmers = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result)
        {
            $programmers[]=$result ;
        }
        $paginatedCollection = new PaginationCollection($programmers, $pagerfanta->getNbResults());


       // var_dump($request->query->all());die;
        $routeParams = array_merge($routeParams, $request->query->all());

        $createLinkUrl = function($targetPage) use ($route, $routeParams) {
            return $this->route->generate($route, array_merge(
                $routeParams,
                array('page' => $targetPage)));
        };

       
       
        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pagerfanta->getNbPages()));
        if ($pagerfanta->hasNextPage())
        {
            $paginatedCollection->addLink('next', $createLinkUrl($pagerfanta->getNextPage()));
        }
        if ($pagerfanta->hasPreviousPage()) {

            $paginatedCollection->addLink('prev', $createLinkUrl($pagerfanta->getPreviousPage()));

        }
        

        return $paginatedCollection;
        
        
        
    }
    
    
}