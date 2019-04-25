<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminPanelController extends AbstractController
{
    /**
     * @Route("/admin/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        //Создание формы
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        //Обработка заявки (POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            //Шифрование пароля
            $password = $passwordEncoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($password);

            //Сохранение пользователя
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Пользователь успешно создан');

            return $this->redirectToRoute('homepage');
        }
        return $this->render('admin_panel/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
