<?php

namespace App\Form;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use App\Entity\User;
use Ehyiah\QuillJsBundle\DTO\Fields\BlockField\HeaderGroupField;
use Ehyiah\QuillJsBundle\DTO\Fields\BlockField\ListField;
use Ehyiah\QuillJsBundle\DTO\Fields\InlineField\BoldField;
use Ehyiah\QuillJsBundle\DTO\Fields\InlineField\CleanField;
use Ehyiah\QuillJsBundle\DTO\Fields\InlineField\ItalicField;
use Ehyiah\QuillJsBundle\DTO\Fields\InlineField\LinkField;
use Ehyiah\QuillJsBundle\DTO\Fields\InlineField\UnderlineField;
use Ehyiah\QuillJsBundle\DTO\QuillGroup;
use Ehyiah\QuillJsBundle\Form\QuillType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Recipe name',
                'required' => false,
            ])
            ->add('ingredients', EntityType::class, [
                'class' => Ingredients::class,
                'choice_label' => 'name',
                'autocomplete' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->add('description',QuillType::class,[
                'label' => 'Recipe description',
                'required' => false,
                'attr' => [
                    'rows' => 10,
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
                ]
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
            ->add('time', NumberType::class,[
                'label' => 'Cooking time',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
