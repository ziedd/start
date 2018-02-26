<?php

namespace AppBundle\EventListener;


use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiEventListener implements EventSubscriberInterface
{


 public function onkernelException(GetResponseForExceptionEvent $event)
 {
  $e = $event->getException();

  if ($e instanceof ApiProblemException) {
   $apiProblem = $e->getApiProblme();

  }
 else{

  $statusCode= $e instanceof HttpExceptionInterface ? $e->getStatusCode():500;

  $apiProblem = new ApiProblem($statusCode);
  if($e instanceof HttpExceptionInterface) {
   $apiProblem->set('detail', $e->getMessage());
  }
 }
  //  $apiProblem = $e->getApiProblme();

  $response = new JsonResponse(

      $apiProblem->toArray(),
      $apiProblem->getStatusCode()


  );
  $response->headers->set('Content-Type', 'application/problem+json');
  $event->setResponse($response);

}





 public static function getSubscribedEvents()
 {
   return [ KernelEvents::EXCEPTION => 'onkernelException' ];
 }


}