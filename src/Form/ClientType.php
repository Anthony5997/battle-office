<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('address_line1')
            ->add('address_line2')
            ->add('city')
            ->add('zipcode')
            ->add('country', ChoiceType::class, [
                'choices'  => [
                    'France' => 'France',
                    'Belgique' => 'Belgique',
                    'Suisse' => 'Suisse']
            ])
            ->add('email')
            ->add('phone')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
