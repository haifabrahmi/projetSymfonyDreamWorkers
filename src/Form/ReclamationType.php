<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('categorie_reclamation', ChoiceType::class, [
            'choices' => [
                'Chauffeur' => 'Chauffeur',
                'Voyageur' => 'Voyageur',
            ],
            'expanded' => false,
            'multiple' => false,
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ])
        ->add('objet_reclamation', null, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 50]),
            ],
        ])
        ->add('description_reclamation', TextareaType::class, [
            'attr' => [
                'rows' => 5 // nombre de lignes à afficher
            ],
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ])
         ->add('etat_reclamation', ChoiceType::class, [
            'choices' => [
                'Traitée' => 1,
                'En cours de traitement' => 0,
            ],
            'expanded' => true,
            'multiple' => false,
            'placeholder' => false,
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]) 
        ->add('date_reclamation', DateType::class, [
            'widget' => 'single_text',
            'attr' => ['placeholder' => 'Date...'],
            'constraints' => [
                new Assert\NotBlank(),
                new GreaterThanOrEqual('today'),

            ],
        ]);
}


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
