<?php

namespace App\Form;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use App\Entity\User;
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
            ->add('description',TextareaType::class,[
                'label' => 'Recipe description',
                'required' => false,
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
