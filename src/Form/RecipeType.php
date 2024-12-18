<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Enum\MealType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
                'class' => MealType::class,
                'autocomplete' => true,
                'choice_label' => 'value',
                'placeholder' => 'Dinner',
            ])
            ->add('people', NumberType::class, [
                'label' => 'Serves people',
                'required' => false,
                'attr' => [
                    'placeholder' => '4',
                ]
            ])
            ->add('ingredients', LiveCollectionType::class, [
                'required' => false,
                'label' => false,
                'entry_type' => IngredientType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'error_bubbling' => false,
                'constraints' => [
                    new Count(['min' => 1]),
                ],
                'mapped' => false,
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
                'label' => 'Recipe Instructions',
                'required' => false,
                'attr' => [
                    'rows' => 10,
                    'placeholder' => 'Step by step instructions for making your dish'
                ],
                /*'quill_options' => [
                    QuillGroup::build(
                        new HeaderGroupField(),
                        new BoldField(),
                        new ItalicField(),
                        new ListField(),
                        new UnderlineField(),
                        new cleanField(),
                    )
                ],*/
            ])
            ->add('image', VichFileType::class, [
                'label' => 'Recipe image',
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PNG, JPEG or WEBP file',
                    ])
                ]
            ])
            ->add('time', NumberType::class, [
                'label' => 'Cooking time (in minutes)',
                'required' => false,
                'attr' => [
                    'placeholder' => '80',
                ]
            ]);

        if ($options['edit']) {
            $builder
                ->remove('ingredients');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'edit' => null,
        ]);
    }
}
