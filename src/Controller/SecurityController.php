<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        /*Авторизация сотрудника в системе*/
        // Получение ошибки при авторизации
        $error = $authenticationUtils->getLastAuthenticationError();
        // получение последнего введеного пользователем логина
        $lastUsername = $authenticationUtils->getLastUsername();

        $loginForm = $this->renderView('Security/login.html.twig',[
            'last_username' => $lastUsername,
            'error' => $error
        ]);
        return new Response($loginForm);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        /*Выход пользователя из системы*/
        return $this->redirectToRoute("homepage");
    }
}
