<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Enum\MealType;
use Ehyiah\QuillJsBundle\DTO\Fields\BlockField\HeaderGroupField;
use Ehyiah\QuillJsBundle\DTO\Fields\BlockField\ListField;
use Ehyiah\QuillJsBundle\DTO\Fields\InlineField\BoldField;
use Ehyiah\QuillJsBundle\DTO\Fields\InlineField\CleanField;
use Ehyiah\QuillJsBundle\DTO\Fields\InlineField\ItalicField;
use Ehyiah\QuillJsBundle\DTO\Fields\InlineField\UnderlineField;
use Ehyiah\QuillJsBundle\DTO\QuillGroup;
use Ehyiah\QuillJsBundle\Form\QuillType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RecipeAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['step'] === 1) {
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
                    'placeholder' => 'Dinner',
                ])
                ->add('people', NumberType::class, [
                    'label' => 'Serves people',
                    'required' => false,
                    'attr' => [
                        'placeholder' => '4',
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
                    ]
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
                ]);
        }

        if ($options['step'] === 3) {
            $builder
                ->add('instructions', QuillType::class, [
                    'label' => 'Recipe Instructions',
                    'required' => false,
                    'quill_extra_options' => [
                        'height' => '600px',
                        'theme' => 'snow',
                        'placeholder' => 'Step 1. Boil water',
                    ],
                    'quill_options' => [
                        QuillGroup::build(
                            new HeaderGroupField(),
                            new BoldField(),
                            new ItalicField(),
                            new ListField(),
                            new UnderlineField(),
                            new cleanField(),
                        )
                    ],
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
        ]);
    }
}
