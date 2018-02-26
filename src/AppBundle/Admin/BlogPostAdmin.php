<?php
/**
 * Created by PhpStorm.
 * User: challouf
 * Date: 25/12/17
 * Time: 16:20
 */
namespace AppBundle \ Admin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BlogPostAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text')
            ->add('body', 'textarea')
            ->add('category', 'entity', array(
                'class' => 'AppBundle\Entity\Category',
                'property' => 'name',
            ))
        ;
        

    }

    protected function configureListFields(ListMapper $listMapper)
    {
        // ... configure $listMapper
    }
}