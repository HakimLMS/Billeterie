<?php

namespace P4\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use P4\BookingBundle\Form\Type\TicketsType;

class BooksType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', DateType::class, array(    
                    'widget' => 'single_text',
                    'html5' => false,
                    'data_class' => 'DateTime',
                    'format' => 'yyyy-MM-dd'
                    ))
                ->add('mail', EmailType::class)
                ->add('name', TextType::class)
                ->add('surname', TextType::class)
                ->add('country', CountryType::class)
                ->add('ticket', CollectionType::class, array(
                    'entry_type' => TicketsType::class,
                    'allow_add' => true,
                    'allow_delete' => false
                ))
                ->add('valider', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4\BookingBundle\Entity\Books'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'p4_bookingbundle_books';
    }


}
