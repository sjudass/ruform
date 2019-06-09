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
    /*Создание формы для регистрации пользователя*/
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Электронная почта', 'attr' => ['class' => 'form-control']])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Пароль', 'attr' => ['class' => 'form-control']),
                'second_options' => array('label' => 'Повторите пароль', 'attr' => ['class' => 'form-control']),
            ))
            ->add('lastname', TextType::class, ['label' => 'Фамилия', 'attr' => ['class' => 'form-control']])
            ->add('firstname', TextType::class, ['label' => 'Имя', 'attr' => ['class' => 'form-control']])
            ->add('middlename', TextType::class, ['label' => 'Отчество', 'attr' => ['class' => 'form-control'], 'required' => false])
            ->add('roles', ChoiceType::class,[
                'choices' => [
                    'Администратор' => 'ROLE_ADMIN',
                    'Менеджер' => 'ROLE_MANAGER',
                    'Оператор' => 'ROLE_OPERATOR',
                ],
                'attr' => ['class' => 'form-control']
            ])
        ;

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return $rolesArray ? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ))
        ;
    }
    /*Создание настроек для формы регистрации*/
    public function configureOptions(OptionsResolver $resolver)
    {
        $roles = new User();
        $resolver->setDefaults([
            'data_class' => User::class,
            'roles' => $roles->getRoles()[0],
        ]);
    }
}
