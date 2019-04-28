<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Электронная почта'])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Пароль'),
                'second_options' => array('label' => 'Повторите пароль'),
            ))
            ->add('lastname', TextType::class, ['label' => 'Фамилия'])
            ->add('firstname', TextType::class, ['label' => 'Имя'])
            ->add('middlename', TextType::class, ['label' => 'Отчество', 'required' => false])
            ->add('roles', ChoiceType::class,[
                'choices' => [
                    'Администратор' => 'ROLE_ADMIN',
                    'Старший менеджер' => 'ROLE_STMANAGER',
                    'Менеджер' => 'ROLE_MANAGER',
                    'Оператор' => 'ROLE_OPERATOR',
                ],
            ])
        ;

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $roles = new User();
        $resolver->setDefaults([
            'data_class' => User::class,
            'roles' => $roles->getRoles()[0],
        ]);
    }
}
