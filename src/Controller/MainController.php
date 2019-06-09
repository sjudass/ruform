<?php

namespace App\Controller;

use App\Entity\Applications;
use App\Entity\Client;
use App\Entity\Dialog;
use App\Entity\DialogMessages;
use App\Repository\DialogRepository;
use \DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;



class MainController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        /*Создание сессии*/
        $this->session = $session;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        /*Отобржение главной страницы*/
        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        /*Отображение страницы с информацией о компании*/
        return $this->render('main/about.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        /*Отображение страницы панель администратора*/
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/services", name="services")
     */
    public function services()
    {
        /*Отображение формы оформления заявки*/
        return $this->render('main/services.html.twig');
    }

    /**
     * @Route("/chat", name="chat")
     */
    public function chat(Request $request, SessionInterface $session)
    {
        /*Регистрация клиента в модуле "Онлайн консультант"*/
        $date = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
        $date->setTimeZone(new \DateTimeZone('Europe/Moscow'));

        if ($request->isMethod('POST'))
        {
            $repository = $this->getDoctrine()->getRepository(Client::class);
            $Client = $repository->findOneBy(['email' => $request->request->get('email')]);
            $entityManager = $this->getDoctrine()->getManager();
            if (!$Client)
            {
                $client = new Client();
                $client->setLastname($request->request->get('lastname'));
                $client->setFirstname($request->request->get('firstname'));
                $client->setMiddlename($request->request->get('middlename'));
                $client->setEmail($request->request->get('email'));
                $client->setPhone($request->request->get('phone'));
                $client->setDateApplication($date);
                $entityManager->persist($client);
                $entityManager->flush();
                $Client = $repository->findOneBy(['email' => $request->request->get('email')]);
            }
            else
            {
                $Client->setDateApplication($date);
                $entityManager->persist($Client);
                $entityManager->flush();
            }
            $dialog = new Dialog();
            $dialog->setTitle($request->request->get('title'));
            $dialog->setAuthorId($Client);
            $dialog->setOperatorId(null);
            $dialog->setTimeCreate($date);
            $entityManager->persist($dialog);
            if ($entityManager->flush() === null)
            {
                $repository = $this->getDoctrine()->getRepository(Dialog::class);
                $Dialog = $repository->findBy(['author' => $Client],['id' => 'DESC'], 1);
                $session->set('client', ['id' => $Client->getId(), 'ip' => $request->getClientIp()]);
                $session->set('dialog', ['id' => $Dialog, 'title' => $request->request->get('title')]);
            }
            else
            {
                return new Response('<html><body><h1>Ошибка создания диалога</h1></body>');
            }

            $repository = $this->getDoctrine()->getRepository(DialogMessages::class);
            $messages = $repository->findBy(['dialog' => $Dialog]);

            $chatForm = $this->renderView('main/dialog.html.twig', ['messages' => $messages]);
            return new Response($chatForm);
        }
        $chatForm = $this->renderView('main/chat.html.twig');
        return new Response($chatForm);
    }

    /**
     * @Route("/dialog/{slug}", name="dialog")
     */
    public function dialog($slug, Request $request, SessionInterface $session)
    {
        /*Отображение диалога клиента с оператором*/
        $repository = $this->getDoctrine()->getRepository(Dialog::class);
        $Dialog = $repository->findOneBy(['id' => $slug]);
        if ($request->isMethod('POST'))
        {
            /*Отправка сообщения от клиента оператору в модуле "Онлайн консультант"*/
            $date = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
            $date->setTimeZone(new \DateTimeZone('Europe/Moscow'));

            $entityManager = $this->getDoctrine()->getManager();
            $message = new DialogMessages();
            $message->setDialogId($Dialog);
            $message->setIsRead(false);
            $message->setIsOperator(false);
            $message->setMessageTime($date);
            $message->setMessageText($request->request->get('message'));
            $message->setAuthorIp($session->get('client')['ip']);
            $entityManager->persist($message);
            $entityManager->flush();

            $repository = $this->getDoctrine()->getRepository(DialogMessages::class);
            $messages = $repository->findBy(['dialog' => $Dialog]);

            $chatForm = $this->renderView('main/dialog_messages.html.twig', ['messages' => $messages]);
            return new Response($chatForm);
        }

        $repository = $this->getDoctrine()->getRepository(DialogMessages::class);
        $messages = $repository->findBy(['dialog' => $Dialog]);

        $chatForm = $this->renderView('main/dialog_messages.html.twig', ['messages' => $messages]);
        return new Response($chatForm);
    }

    /**
     * @Route("/consult", name="consult")
     */
    public function consult()
    {
        /*Отображение списка диалогов в модуле "Онлайн консультант" для сотрудника*/
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(Dialog::class);
        if ($user->getRoles()[0] == 'ROLE_OPERATOR')
        {
            $dialogs = $repository->findBy(['operator' => [null, $user->getId()]], ['id' => 'DESC']);
        }
        else
        {
            $dialogs = $repository->findBy([],['id' => 'DESC']);
        }
        $consultForm = $this->renderView('main/consult.html.twig', ['dialogs' => $dialogs]);
        return new Response($consultForm);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request)
    {
        /*Поиск необходимого диалога в модуле "Онлайн консультант"*/
        if ($request->isMethod("POST"))
        {
            $user = $this->getUser();
            $query = $request->request->get("search");

            $repository = $this->getDoctrine()->getRepository(Dialog::class);
            if ($user->getRoles()[0] == 'ROLE_ADMIN')
                $dialogs = $repository->searchByQuery($query);
            else{
                $dialogs = $repository->searchByOperatorQuery($query, $user->getId());
            }

            $consultForm = $this->renderView('main/dialog_list.html.twig', ['dialogs' => $dialogs]);
            return new Response($consultForm);
        }
    }

    /**
     * @Route("/consult/{slug}", name="consult_dialog")
     */
    public function consultDialog($slug, Request $request)
    {
        /*Отображение выбранного диалога сотруднику*/
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(Dialog::class);
        $Dialog = $repository->findOneBy(['id' => $slug]);
        if ($Dialog->getOperator() == null)
        {
            $Dialog->setOperatorId($user);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($Dialog);
        $entityManager->flush();
        if ($request->isMethod('POST'))
        {
            /*Отправка сообщения клиенту*/
            $date = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
            $date->setTimeZone(new \DateTimeZone('Europe/Moscow'));
            $message = new DialogMessages();
            $message->setDialogId($Dialog);
            $message->setIsRead(false);
            $message->setIsOperator(true);
            $message->setMessageTime($date);
            $message->setMessageText($request->request->get('message'));
            $message->setAuthorIp($request->getClientIp());
            $entityManager->persist($message);
            $entityManager->flush();
            $repository = $this->getDoctrine()->getRepository(DialogMessages::class);
            $messages = $repository->findBy(['dialog' => $Dialog]);

            $consultForm = $this->renderView('main/dialog_messages.html.twig', ['messages' => $messages]);
            return new Response($consultForm);
        }
        $repository = $this->getDoctrine()->getRepository(DialogMessages::class);
        $messages = $repository->findBy(['dialog' => $Dialog]);
        $repository = $this->getDoctrine()->getRepository(Dialog::class);
        if ($user->getRoles()[0] == 'ROLE_OPERATOR')
        {
            $dialogs = $repository->findBy(['operator' => [null, $user->getId()]], ['id' => 'DESC']);
        }
        else
        {
            $dialogs = $repository->findBy([],['id' => 'DESC']);
        }
        $consultForm = $this->renderView('main/consult.html.twig', ['messages' => $messages, 'dialogs' => $dialogs, 'current_dialog' => $Dialog]);
        return new Response($consultForm);
    }

    /**
     * @Route("/getconsult/{slug}", name="getconsult_dialog")
     */
    public function getconsultDialog($slug, Request $request)
    {
        /*Отображение диалога для клиента*/
        $repository = $this->getDoctrine()->getRepository(Dialog::class);
        $Dialog = $repository->findOneBy(['id' => $slug]);
        $repository = $this->getDoctrine()->getRepository(DialogMessages::class);
        $messages = $repository->findBy(['dialog' => $Dialog]);

        $consultForm = $this->renderView('main/dialog_messages.html.twig', ['messages' => $messages]);
        return new Response($consultForm);
    }

    /**
     * @Route("/getdialoglist/{slug}", name="getdialoglist")
     */
    public function getdialogList($slug)
    {
        /*Отображение списка диалогов в модуле "Онлайн консультант" для сотрудника*/
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(Dialog::class);
        $Dialog = $repository->findOneBy(['id' => $slug]);
        if ($user->getRoles()[0] == 'ROLE_OPERATOR')
        {
            $dialogs = $repository->findBy(['operator' => [null, $user->getId()]], ['id' => 'DESC']);
        }
        else
        {
            $dialogs = $repository->findBy([],['id' => 'DESC']);
        }
        $consultForm = $this->renderView('main/dialog_list.html.twig', ['dialogs' => $dialogs, 'current_dialog' => $Dialog]);
        return new Response($consultForm);
    }

    /**
     * @Route("/getdialogs", name="getdialogs")
     */
    public function getdialogs()
    {
        /*Отображение выбранного диалога для сотрудника*/
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(Dialog::class);
        if ($user->getRoles()[0] == 'ROLE_OPERATOR')
        {
            $dialogs = $repository->findBy(['operator' => [null, $user->getId()]], ['id' => 'DESC']);
        }
        else
        {
            $dialogs = $repository->findBy([],['id' => 'DESC']);
        }
        $consultForm = $this->renderView('main/dialog_list.html.twig', ['dialogs' => $dialogs]);
        return new Response($consultForm);
    }

    /**
     * @Route("/application", name="application")
     */
    public function application(Request $request, SessionInterface $session)
    {
        /*Оформление заявки клиента*/
        if ($request->isMethod('POST'))
        {
            $date = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
            $date->setTimeZone(new \DateTimeZone('Europe/Moscow'));
            $repository = $this->getDoctrine()->getRepository(Client::class);
            $Client = $repository->findOneBy(['email' => $request->request->get('email')]);
            $entityManager = $this->getDoctrine()->getManager();
            if (!$Client)
            {
                $client = new Client();
                $client->setLastname($request->request->get('lastname'));
                $client->setFirstname($request->request->get('firstname'));
                $client->setMiddlename($request->request->get('middlename'));
                $client->setEmail($request->request->get('email'));
                $client->setPhone($request->request->get('phone'));
                $client->setDateApplication($date);
                $entityManager->persist($client);
                $entityManager->flush();
                $Client = $repository->findOneBy(['email' => $request->request->get('email')]);
            }
            else
            {
                $Client->setDateApplication($date);
                $entityManager->persist($Client);
                $entityManager->flush();
            }

            $application = new Applications();
            $application->setTitle($request->request->get('title'));
            $application->setContent($request->request->get('content'));
            $application->setClient($Client);
            $application->setUser(null);
            $application->setDateCreate($date);
            $application->setDateProcess(null);
            $application->setStatus('Поступила');
            $entityManager->persist($application);
            if ($entityManager->flush() === null)
            {
                $mainForm = $this->renderView('main/index.html.twig');
                return new Response($mainForm );
            }
        }
        return $this->redirectToRoute('homepage');
    }
}