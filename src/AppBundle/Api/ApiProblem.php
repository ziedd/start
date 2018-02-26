<?php

namespace AppBundle\Api;
use Symfony\Component\HttpFoundation\Response;

/**
 * A wrapper for holding data to be used for a application/problem+json response
 */
class ApiProblem
{
    private $statusCode;

    private $type;

    private  $title;

    private $extraData = array();

    const TYPE_VALIDATION_ERROR = 'validation_error';
    
    const TYPE_INVALID_REQUEST_BODY_FORMAT =  'INVALID Json body !';

    private static $titles = [

        self::TYPE_VALIDATION_ERROR => 'There was a validation error',
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => " THERE WAS INVALID Json body !"
    ];


    public function __construct($statusCode,$type=null)
    {
        $this->statusCode = $statusCode;
        if ($type === null) {
            $type = 'about:blank';
            $title = isset(Response::$statusTexts[$statusCode])
                           ? Response::$statusTexts[$statusCode]
                             : 'Unknown status code :(';

        }

        else{

            if (!isset(self::$titles[$type])) {
            throw new \InvalidArgumentException('NO TITLE FOR TYPE!!!!!   ' . $type);
            }
               $title = self::$titles[$type];
             }
        $this->type = $type;
        $this->title = $title;
    }

    public function toArray()
    {
        return array_merge(
            $this->extraData,
            array(
                'status' => $this->statusCode,
                'type' => $this->type,
                'title' =>  $this->title
             )
        );
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
