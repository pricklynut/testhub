<?php

namespace AppBundle\Form;

use AppBundle\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('question', TextareaType::class, [
            'label' => 'Вопрос',
        ]);

        $builder->add('variants', CollectionType::class, [
            'entry_type' => VariantFormType::class,
            'allow_add' => true,
            'label' => false,
            'prototype_name' => '__variant_number__',
            'by_reference' => false,
        ]);

        $builder->add('type', ChoiceType::class, [
            'label' => 'Тип вопроса:',
            'multiple' => false,
            'expanded' => true,
            'choices' => [
                'Ввести слово' => Question::TYPE_STRING_TYPEIN,
                'Ввести число' => Question::TYPE_NUMBER_TYPEIN,
                'Выбрать вариант из списка' => Question::TYPE_SINGLE_VARIANT,
                'Выбрать несколько вариантов' => Question::TYPE_MULTIPLE_VARIANTS,
            ],
            'label_attr' => [
                'class' => 'radio-inline'
            ],
        ]);

        $builder->add('price', IntegerType::class, [
            'required' => true,
            'empty_data' => 1,
            'label' => 'Баллов за ответ',
            'attr' => ['min' => 1, 'max' => 100],
        ]);

        $builder->add('precision', IntegerType::class, [
            'label' => 'Точность (минимальное кол-во знаков после запятой)',
            'empty_data' => 0,
            'required' => false,
            'attr' => ['min' => 0, 'max' => 100],
        ]);

        $builder->add('serialNumber', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
