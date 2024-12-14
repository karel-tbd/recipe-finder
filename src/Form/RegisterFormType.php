<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegisterFormType extends AbstractType
{

    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'name@example.com'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Password',
                'mapped' => false,
                'attr' => [
                    'placeholder' => '••••••••',
                ],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    ],
            ])
            ->add('repeatPlainPassword', PasswordType::class, [
                'label' => 'Repeat password',
                'mapped' => false,
                'attr' => [
                    'placeholder' => '••••••••',
                ],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Callback(function ($object, ExecutionContextInterface $context) {
                        $form = $context->getRoot();
                        $plainPassword = $form->get('plainPassword')->getData();
                        $repeatPlainPassword = $form->get('repeatPlainPassword')->getData();

                        if ($plainPassword !== $repeatPlainPassword) {
                            $context->buildViolation('Passwords do not match')
                                ->atPath('repeatPlainPassword')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                /** @var \App\Entity\User $user */
                $user = $event->getData();
                $form = $event->getForm();
                if ($form->has('plainPassword')) {
                    $plainPassword = $form->get('plainPassword')->getViewData();
                    if (!empty($plainPassword)) {
                        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $plainPassword);
                        $user->setPassword($hashedPassword);
                    }
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
