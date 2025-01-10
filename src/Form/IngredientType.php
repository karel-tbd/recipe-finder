<?php

namespace App\Form;

use App\Entity\Ingredients;
use App\Entity\RecipeIngredients;
use App\Enum\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ingredient', EntityType::class, [
                'label' => 'Ingredient',
                'class' => Ingredients::class,
                'choice_label' => 'name',
                'required' => false,
                'autocomplete' => true,
                'placeholder' => 'Tomatoes',
                'constraints' => [new NotBlank()],
            ])
            ->add('quantity', TextType::class, [
                'label' => 'Quantity',
                'required' => false,
                'attr' => [
                    'placeholder' => '2',
                    'autocomplete' => 'off',
                ],
                'constraints' => [new NotBlank()],
            ])
            ->add('unit', EnumType::class, [
                'label' => 'Unit',
                'class' => Unit::class,
                'required' => false,
                'autocomplete' => true,
                'choice_label' => 'value',
                'placeholder' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredients::class,
        ]);
    }
}
