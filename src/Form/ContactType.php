<?php

namespace App\Form;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ]
            ])
            ->add('object', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Prise de rendez-vous' => 1,
                    'Demande de devis' => 2,
                ],
                'placeholder' => 'Sélectionnez votre objet',
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('rgpd', CheckboxType::class, [
                'label' => 'En soumettant ce formulaire, j\'accepte que les informations saisies soient exploitées dans le cadre d\'une demande et de la relation commerciale qui peut en découler.',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
        ;

        $builder->get('object')->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            $this->addDateField($event);
        });

        $builder->get('object')->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event){
            $this->addDateField($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    private function addDateField(FormEvent $event)
    {
        // dd($event);
        $form = $event->getForm()->getParent();
        $data = $event->getData();

        if($data == 1) {
            $form->add('date', DateType::class, [
                'label' => 'Date de rendez-vous',
                'input' => 'string',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
        }
    }
}
