<?php

namespace matuckGeorgeGrantCoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('attr' => array('class' => 'form-control')))
            ->add('content', 'ckeditor')
            ->add('home', 'checkbox', array('attr' => array('class' => 'form-control'), 'label' => 'Set as Homepage?', 'required' => false))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'matuckGeorgeGrantCoBundle\Entity\Page'
        ));
    }
}
