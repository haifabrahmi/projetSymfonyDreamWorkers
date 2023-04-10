<?php

namespace App\Form;

use App\Entity\Maintenance;
use App\Entity\Bus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

class MaintenanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_entretien', DateType::class, [
                'label' => 'Date d\'entretien',
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir une date d\'entretien.',
                    ]),
                    new Type([
                        'type' => 'DateTime',
                        'message' => 'Veuillez saisir une date valide.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une description.',
                    ]),
                    new Type([
                        'type' => 'string',
                        'message' => 'Veuillez saisir une description valide.',
                    ]),
                ],
            ])
            ->add('bus', EntityType::class, [
                'class' => Bus::class,
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez sélectionner un bus.',
                    ]),
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Maintenance::class,
        ]);
    }
}
