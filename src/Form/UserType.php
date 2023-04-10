<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TypeTextType::class,[
                'attr'=>[
                    'class' => 'form-control input-rounded',
                    'placeholder' => 'veuillez saisir votre nom'
                ]
            ])
            ->add('prenom',TypeTextType::class,[
                'attr'=>[
                    'class' => 'form-control input-rounded',
                    'placeholder' => 'veuillez saisir votre prénom'
                ]
            ])
            ->add('email',TypeTextType::class,[
                'attr'=>[
                    'class' => 'form-control input-rounded',
                    'placeholder' => 'veuillez saisir votre email'
                ]
            ])
            ->add('number',TypeTextType::class,[
                'attr'=>[
                    'class' => 'form-control input-rounded',
                    'placeholder' => 'veuillez saisir votre numéro'
                ]
            ])
            ->add('password',TypeTextType::class,[
                'attr'=>[
                    'class' => 'form-control input-rounded',
                    'placeholder' => 'veuillez saisir votre mot de passe'
                ]
            ])
            ->add('role', ChoiceType::class, [
                'attr'=>[
                    'class' => 'form-control input-rounded',
                ],
                'choices' => [
                    'admin' => 'admin',
                    'voyageur' => 'voyageur',
                    'chauffeur' => 'chauffeur',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
