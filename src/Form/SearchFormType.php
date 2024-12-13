<?php

namespace App\Form;

use App\Entity\General\Company;
use App\Entity\Ingredients;
use App\Service\FormService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
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
                'placeholder' => 'Search',
                'autocomplete' => true,
                'multiple' => true,
                'required' => false,
                'data' => !empty($options['search']['search']) ? $this->formService->getEntityReferences(Ingredients::class, $options['search']['search']) : null,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'search' => [],
        ]);
    }
    public function getBlockPrefix(): string
    {
        return 'search';
    }
}
