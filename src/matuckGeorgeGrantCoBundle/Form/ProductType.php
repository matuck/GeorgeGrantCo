<?php

namespace matuckGeorgeGrantCoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FM\ElfinderBundle\Form\Type\ElFinderType;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('logo', 'FM\ElfinderBundle\Form\Type\ElFinderType', array('instance'=>'form', 'enable'=>true))
            ->add('name', 'text', array('attr' => array('class' => 'form-control')))
            ->add('category', 'entity', array('class' => 'matuckGeorgeGrantCoBundle:ProductCategory', 'choice_label' => 'name', 'attr' => array('class' => 'form-control')))
            ->add('url', 'text', array('attr' => array('class' => 'form-control')))
            ->add('displayurl', 'text', array('attr' => array('class' => 'form-control')))
            ->add('content', 'ckeditor')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'matuckGeorgeGrantCoBundle\Entity\Product'
        ));
    }
}
