<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\AddressBilling;
use App\Form\ClientType;
use App\Form\AddressBillingType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\ProductType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', ClientType::class)
            ->add('address_billing', AddressBillingType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
