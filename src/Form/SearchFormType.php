<?php

namespace App\Form;

use App\Entity\Ingredients;
use App\Service\FormService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function __construct(private readonly FormService $formService)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', EntityType::class, [
                    'label' => 'Search on ingredients',
                    'class' => Ingredients::class,
                    'choice_label' => 'Name',
                    'autocomplete' => true,
                    'multiple' => true,
                    'required' => false,
                    'data' => !empty($options['search']['search']) ? $this->formService->getEntityReferences(Ingredients::class, $options['search']['search']) : null,
                ]
            )
            ->add('name', TextType::class, [
                'label' => 'Search on recipe name',
                'required' => false,
                'data' => $options['search']['name'] ?? null,
            ])
            ->add('meat', CheckboxType::class, [
                'label' => 'Meat',
                'required' => false,
            ])
            ->add('seaFood', CheckboxType::class, [
                'label' => 'Sea Food',
                'required' => false,
            ])
            ->add('vegetarian', CheckboxType::class, [
                'label' => 'Vegetarian',
                'required' => false,
            ])
            ->add('proteinFoods', CheckboxType::class, [
                'label' => 'Protein Foods',
                'required' => false,
            ])
            ->add('pastas', CheckboxType::class, [
                'label' => 'Pastas',
                'required' => false,
            ])
            ->add('breakfast', CheckboxType::class, [
                'label' => 'Breakfast',
                'required' => false,
            ])
            ->add('brunch', CheckboxType::class, [
                'label' => 'Brunch',
                'required' => false,
            ])
            ->add('lunch', CheckboxType::class, [
                'label' => 'Lunch',
                'required' => false,
            ])
            ->add('dinner', CheckboxType::class, [
                'label' => 'Dinner',
                'required' => false,
            ])
            ->add('cocktail', CheckboxType::class, [
                'label' => 'Cocktail',
                'required' => false,
            ])
            ->add('nuts', CheckboxType::class, [
                'label' => 'Nuts',
                'required' => false,
            ])
            ->add('gluten', CheckboxType::class, [
                'label' => 'Gluten',
                'required' => false,
            ])
            ->add('seaFoodAllergies', CheckboxType::class, [
                'label' => 'Sea Food',
                'required' => false,
            ])
            ->add('dairy', CheckboxType::class, [
                'label' => 'Dairy',
                'required' => false,
            ])
            ->add('cuisine', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Italian' => 'italian',
                    'Mexican' => 'mexican',
                    'Greek' => 'greek',
                    'Chinese' => 'chinese',
                    'Japanese' => 'japanese',
                    'French' => 'french',
                    'American' => 'american',
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'search' => [],
        ]);
    }
}
