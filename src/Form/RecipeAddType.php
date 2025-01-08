<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Enum\MealType;
use Doctrine\ORM\Exception\ORMException;
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
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RecipeAddType extends AbstractType
{
    public function __construct()
    {
    }

    /**
     * @throws ORMException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['step'] === 1) {
            $builder
                ->add('name', TextType::class, [
                    'label' => 'Recipe name',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Grilled Steak',
                    ],
                    'constraints' => [new NotBlank()],
                    'data' => $options['recipeValues']['name'] ?? null,
                ])
                ->add('mealType', EnumType::class, [
                    'label' => 'Meal type',
                    'required' => false,
                    'multiple' => true,
                    'class' => MealType::class,
                    'choice_label' => fn(MealType $mealType) => $mealType->value,
                    'autocomplete' => true,
                    'placeholder' => 'Dinner',
                    'constraints' => [new NotBlank()],
                    //'data' => !empty($options['recipeValues']['mealType']) ? $this->formService->getEnumReference(MealType::class, $options['recipeValues']['mealType'][0]) : null,
                ])
                ->add('people', NumberType::class, [
                    'label' => 'Serves people',
                    'required' => false,
                    'attr' => [
                        'placeholder' => '4',
                    ],
                    'constraints' => [new NotBlank()],
                    'data' => $options['recipeValues']['people'] ?? null,
                ])
                ->add('description', TextareaType::class, [
                    'label' => 'Recipe description',
                    'required' => false,
                    'attr' => [
                        'rows' => 5,
                        'placeholder' => 'Short description of your dish'
                    ],
                    'constraints' => [new NotBlank()],
                    'data' => $options['recipeValues']['description'] ?? null,
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
                        ])
                    ],
                ])
                ->add('time', NumberType::class, [
                    'label' => 'Cooking time (in minutes)',
                    'required' => false,
                    'attr' => [
                        'placeholder' => '80',
                    ],
                    'constraints' => [new NotBlank()],
                    'data' => $options['recipeValues']['time'] ?? null,
                ]);
        }

        if ($options['step'] === 2) {
            $builder
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
                        new Valid(),
                    ],
                ]);
        }

        if ($options['step'] === 3) {
            $builder
                ->add('instructions', TextareaType::class, [
                    'label' => 'Recipe Instructions',
                    'required' => false,
                    'constraints' => [
                        new NotBlank(
                            ['message' => 'This value should not be blank.']
                        ),
                    ]
                ])
                ->add('publish', CheckboxType::class, [
                    'label' => 'Publish',
                    'required' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'step' => 1,
            'recipeValues' => [],
        ]);
    }
}
