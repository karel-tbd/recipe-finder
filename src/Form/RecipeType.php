<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Enum\MealCountry;
use App\Enum\MealType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /* @var Recipe $recipe */
        $builder
            ->add('name', TextType::class, [
                'label' => 'Recipe name',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Grilled Steak',
                ]
            ])
            ->add('mealType', EnumType::class, [
                'label' => 'Meal type',
                'required' => false,
                'multiple' => true,
                'class' => MealType::class,
                'choice_label' => fn(MealType $mealType) => $mealType->value,
                'autocomplete' => true,
                'attr' => [
                    'placeholder' => 'Dinner',
                ]
            ])
            ->add('country', EnumType::class, [
                'label' => 'Country',
                'required' => false,
                'multiple' => false,
                'class' => MealCountry::class,
                'choice_label' => fn(MealCountry $mealCountry) => $mealCountry->value,
                'autocomplete' => true,
                'attr' => [
                    'placeholder' => 'None',
                ]
            ])
            ->add('people', NumberType::class, [
                'label' => 'Serves people',
                'required' => false,
                'attr' => [
                    'placeholder' => '4',
                ]
            ])
            ->add('recipeIngredients', LiveCollectionType::class, [
                'required' => false,
                'label' => false,
                'entry_type' => IngredientType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'constraints' => [
                    new Count(['min' => 1]),

                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Recipe description',
                'required' => false,
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Short description of your dish'
                ],
            ])
            ->add('instructions', TextareaType::class, [
                'label' => 'Instructions',
                'required' => false,
                'constraints' => [
                    new NotBlank(
                        ['message' => 'This value should not be blank.']
                    ),
                ]
            ])
            ->add('image', VichFileType::class, [
                'label' => 'Recipe image',
                'required' => false,
                'allow_delete' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PNG, JPEG or WEBP file',
                    ]),
                    new NotBlank([

                    ])
                ],
            ])
            ->add('time', NumberType::class, [
                'label' => 'Cooking time (in minutes)',
                'required' => false,
                'attr' => [
                    'placeholder' => '80',
                ]
            ])
            ->add('publish', CheckboxType::class, [
                'label' => 'Publish',
                'required' => false,
            ]);

        if ($options['edit']) {
            $builder
                ->remove('image')
                ->add('image', VichFileType::class, [
                    'label' => 'Recipe image',
                    'required' => false,
                    'allow_delete' => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/webp',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid PNG, JPEG or WEBP file',
                        ]),
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'edit' => null
        ]);
    }
}
