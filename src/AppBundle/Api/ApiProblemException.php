<?php
/**
 * Created by PhpStorm.
 * User: challouf
 * Date: 09/09/17
 * Time: 23:01
 */

namespace AppBundle\Api;


use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{

    private $apiProblme;
    public function __construct( ApiProblem $apiProblem, \Exception $previous = null, array $headers = array(), $code = 0)


    {
        $this->apiProblme =$apiProblem;
        $statusCode = $apiProblem->getStatusCode();
        $message = $apiProblem->getTitle();
        parent::__construct($statusCode, $message, $previous, $headers, $code);

    }

    /**
     * @return ApiProblem
     */
    public function getApiProblme()
    {
        return $this->apiProblme;
    }



}