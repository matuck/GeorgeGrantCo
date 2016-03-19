<?php

namespace matuckGeorgeGrantCoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LiteratureType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title','text', array('attr' => array('class' => 'form-control')))
            ->add('cover','FM\ElfinderBundle\Form\Type\ElFinderType', array('instance'=>'litform', 'enable'=>true,'attr' => array('class' => 'form-control')))
            ->add('file', 'FM\ElfinderBundle\Form\Type\ElFinderType', array('instance'=>'litform', 'enable'=>true,'attr' => array('class' => 'form-control')))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'matuckGeorgeGrantCoBundle\Entity\Literature'
        ));
    }
}
