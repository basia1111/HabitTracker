<?php

namespace App\Form;

use App\Entity\Habit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class EditHabitFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a habit name',
                    ]),
                ],
               
            ])
            ->add('hasTime', CheckboxType::class, [
                'required' => false,
                'mapped' => false,
                'label' => false,
                'label' => 'Set specyfic time',
            ])
            ->add('time', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'single_text',
                'required' => false, 
                'label' => false,
               
            ])
            ->add('frequency', ChoiceType::class, [
                'choices' => [
                    'Daily' => 'daily',
                    'Weekdays' => 'weekdays',
                    'Weekends' => 'weekends',
                    'Selected Days' => 'days'
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => false, 
                'placeholder' => false,
            ])
            ->add('weekDays', ChoiceType::class, [
                'choices' => [
                    'Monday' => 'mon',
                    'Tuesday' => 'tue',
                    'Wednesday' => 'wed',
                    'Thursday' => 'thu',
                    'Friday' => 'fri',
                    'Saturday' => 'sat',
                    'Sunday' => 'sun'
                ],
                'expanded' => true,
                'multiple' => true, 
                'required' => false, 
            ])
            ->add('color', ChoiceType::class, [
                'choices' => [
                    '#FFFFFF' => '0',
                    '#a4bdfc' => '1',
                    '#7ae7bf' => '2',
                    '#dbadff' => '3',
                    '#ff887c' => '4',
                    '#fbd75b' => '5',
                    '#ffb878' => '6',
                    '#46d6db' => '7',
                    '#e1e1e1' => '8',
                    '#5484ed' => '9',
                    '#51b749' => '10',
                    '#dc2127' => '11',
                ],
                'expanded' => true,
                'multiple' => false,
            ]);

    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Habit::class,
        ]);
    }
}
