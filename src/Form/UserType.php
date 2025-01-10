<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
            ])
            ->add('firstName', TextType::class, [
                'label' => 'First name',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last name',
                'required' => false,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'required' => false,
                'data' => '',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ]
            ])
            ->add('passwordRepeat', PasswordType::class, [
                    'label' => 'Repeat Password',
                    'required' => false,
                    'mapped' => false,
                    'data' => '',
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 4096,
                        ]),
                        new Callback(function ($object, ExecutionContextInterface $context) {
                            $form = $context->getRoot();
                            $plainPassword = $form->get('password')->getData();
                            $repeatPlainPassword = $form->get('passwordRepeat')->getData();

                            if ($plainPassword !== $repeatPlainPassword) {
                                $context->buildViolation('Passwords do not match')
                                    ->atPath('repeatPlainPassword')
                                    ->addViolation();
                            }
                        }),
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
