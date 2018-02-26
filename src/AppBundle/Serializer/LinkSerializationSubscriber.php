<?php
/**
 * Created by PhpStorm.
 * User: challouf
 * Date: 16/09/17
 * Time: 23:31
 */

namespace AppBundle\Serializer;



use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

class LinkSerializationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {

        return [
          [
              'event' => 'serializer.post_serialize',
              'method' => 'onPostSerialize',
              'format' => 'json',
              'class' => 'AppBundle\Entity\Programmer'
          ]
        ];

    }


    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var JsonSerializationVisitor $visitor */
           $visitor = $event->getVisitor();
        $visitor->addData('uri', 'FOO');

    }

}