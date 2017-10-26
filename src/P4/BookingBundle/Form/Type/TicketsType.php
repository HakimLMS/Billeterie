<?php

namespace P4\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ChoiceType::class, array(
                    "choices" => array("Journée"=> true, "Demi-journée" => false),
                    "label" => 'Type de visite'
                ))
                ->add('name', TextType::class, array (
                    'label' => 'Nom'
                ))
                ->add('surname', TextType::class, array(
                    'label' => 'Prénom'
                ))
                ->add('birthDate', DateType::class, array(    
                    'widget' => 'single_text',
                    'html5' => false,
                    'data_class' => 'DateTime',
                    'format' => 'yyyy-MM-dd',
                    'attr' => array('class' => 'js-datepicker'),
                    'label' => 'Date de Naissance'
                    ))
                ->add('discount', ChoiceType::class, array(
                    "choices" => array("Oui"=> true, "Non"=> false),
                    'label' => 'Réduction'));                
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4\BookingBundle\Entity\Tickets'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'p4_bookingbundle_tickets';
    }


}
