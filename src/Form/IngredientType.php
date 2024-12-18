<?php

namespace App\Form;

use App\Entity\Ingredients;
use PHPUnit\TextUI\XmlConfiguration\Logging\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'multiple' => false,
            ])
            ->add('quantity', TextType::class,[
                'label' => 'Quantity',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
