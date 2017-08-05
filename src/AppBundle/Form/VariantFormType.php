<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class VariantFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();

        $builder->add('answer', ChoiceType::class, [
            'label' => false,
            'expanded' => $data['expanded'],
            'multiple' => $data['multiple'],
            'choices' => $data['choices'],
        ])
        ->add('save', SubmitType::class, ['label' => 'Ответить']);
    }
}
