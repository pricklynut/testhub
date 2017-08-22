<?php

namespace AppBundle\Form;

use AppBundle\Entity\Test;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'Название',
        ]);

        $builder->add('description', TextareaType::class, [
            'label' => 'Описание',
            'required' => false,
        ]);

        $builder->add('timeLimit', IntegerType::class, [
            'label' => 'Время на прохождение в минутах '
                        .'(оставьте 0, если ограничение по времени не нужно)',
            'required' => true,
            'empty_data' => 0,
            'attr' => ['min' => 0, 'max' => 1000],
        ]);

        $builder->add('questions', CollectionType::class, [
            'entry_type' => QuestionFormType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'label' => false,
            'prototype_name' => '__question_number__',
        ]);

        $builder->add('showAnswers', CheckboxType::class, [
            'required' => false,
            'label' => 'Показывать правильные ответы после прохождения',
        ]);

        $builder->add('tags', CollectionType::class, [
            'label' => false,
            'required' => true,
            'entry_type' => TagFormType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__tag_number__',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
        ]);
    }
}
