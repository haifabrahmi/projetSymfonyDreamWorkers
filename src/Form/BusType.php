<?php

namespace App\Form;

use App\Entity\Bus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class BusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modele', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ "modele" ne peut pas être vide',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 50,
                        'minMessage' => 'Le champ "modele" doit avoir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le champ "modele" ne peut pas avoir plus de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('numero_de_plaque', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ "numero_de_plaque" ne peut pas être vide',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 20,
                        'minMessage' => 'Le champ "numero_de_plaque" doit avoir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le champ "numero_de_plaque" ne peut pas avoir plus de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('capacite', null, [
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 50,
                        'minMessage' => 'La capacité doit être au moins {{ limit }}.',
                        'maxMessage' => 'La capacité ne peut pas dépasser {{ limit }}.',
                    ]),
                ],
            ])

            ->add('destination')
            ->add('date_depart', ChoiceType::class, [
                'choices' => $this->getAvailableDates(60),
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('date_arrive', ChoiceType::class, [
                'choices' => $this->getAvailableDates(60),
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'label' => 'Image',
                'data_class' => null,
            ])
           
                
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bus::class,
        ]);
    }

    private function getAvailableDates(int $days): array
    {
        $choices = [];
        $today = new \DateTime();
        $endDate = clone $today;
        $endDate->modify(sprintf('+%d days', $days));
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($today, $interval, $endDate);

        foreach ($period as $date) {
            $choices[$date->format('d/m/Y')] = $date;
        }

        return $choices;
    }
    
}

