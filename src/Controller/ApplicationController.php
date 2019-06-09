<?php

namespace App\Controller;

use App\Entity\Applications;
use App\Entity\User;
use App\Entity\Client;
use App\Repository\ApplicationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \DateTime;


/**
 * @Route("/admin/applications")
 */
class ApplicationController extends AbstractController
{
    /**
     * @Route("/", name="applications_index", methods={"GET"})
     */
    public function index(ApplicationsRepository $applicationsRepository): Response
    {
        /*Отображение страницы со списком заявок*/
        $user = $this->getUser();
        if ($user->getRoles()[0] == 'ROLE_MANAGER')
        {
            $applications = $applicationsRepository->findBy(['user' => [null, $user->getId()]]);
        }
        else
        {
            $applications = $applicationsRepository->findBy([]);
        }
        return $this->render('application/index.html.twig', [
            'applications' => $applications,
        ]);
    }

    /**
     * @Route("/statistic", name="statistic")
     */
    public function statistic(ApplicationsRepository $applicationsRepository)
    {
        /*Отображение круговой диаграммы со статистикой заявок*/
        $status_entered = count($applicationsRepository->findBy(['status' => 'Поступила']));
        $status_consideration = count($applicationsRepository->findBy(['status' => 'На рассмотрении']));
        $status_queue = count($applicationsRepository->findBy(['status' => 'В очереди']));
        $status_accepted = count($applicationsRepository->findBy(['status' => 'Выполнена']));
        $status_rejected = count($applicationsRepository->findBy(['status' => 'Отказано']));
        $status_all = count($applicationsRepository->findAll());
        $status[] = [
            'Поступила' => $status_entered,
            'На рассмотрении' => $status_consideration,
            'В очереди' => $status_queue,
            'Выполнена' => $status_accepted,
            'Отказано' => $status_rejected,
            'Всего' => $status_all
        ];
        return new JsonResponse($status);
    }

    /**
     * @Route("/user={id}", name="application_user", methods={"GET"})
     */
    public function application_user(User $user, ApplicationsRepository $applicationsRepository): Response
    {
        /*Отображение страницы со списком заявок, обработанных выбранным сотрудником*/
        return $this->render('application/appUser.html.twig', [
            'applications' => $applicationsRepository->findBy(['user' => $user]),
            'user' => $user,
        ]);
    }


    /**
     * @Route("/client={id}", name="application_client", methods={"GET"})
     */
    public function application_client(Client $client, ApplicationsRepository $applicationsRepository): Response
    {
        /*Отображение страницы со списком заявок, оформленных выбранным клиентом*/
        $user = $this->getUser();
        if ($user->getRoles()[0] == 'ROLE_MANAGER')
        {
            $applications = $applicationsRepository->findBy(['client' => $client, 'user' => [null, $user->getId()]]);
        }
        else
        {
            $applications = $applicationsRepository->findBy(['client' => $client]);
        }
        return $this->render('application/appClient.html.twig', [
            'applications' => $applications,
            'client' => $client,
        ]);
    }

    /**
     * @Route("/export", name="application_export")
     */
    public function export(ApplicationsRepository $applicationsRepository)
    {
        /*Выгрузка в Excel списка заявок*/
        $applications = $applicationsRepository->findAll();
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'Список заявок');
        $sheet->getStyle('A1')->getFont()->setSize(16);
        $sheet->setCellValue('A2', '№');
        $sheet->setCellValue('B2', 'Тема обращения');
        $sheet->setCellValue('C2', 'Клиент');
        $sheet->setCellValue('D2', 'Дата создания');
        $sheet->setCellValue('E2', 'Дата обработки');
        $sheet->setCellValue('F2', 'Статус');
        $sheet->setCellValue('G2', 'Сотрудник');
        $sheet->getStyle('A2:G2')->getFont()->setSize(14);

        foreach ($applications as $application)
        {
            if ($application->getUser() !== null)
            {
                $apps[] = [
                    "id" => $application->getId(),
                    "title" => $application->getTitle(),
                    "client" => $application->getClient()->getLastname()." ". $application->getClient()->getFirstname()." ".$application->getClient()->getMiddlename(),
                    "date_create" => $application->getDateCreate()->format("d.m.Y"),
                    "data_process" => $application->getDateProcess()->format("d.m.Y"),
                    "status" => $application->getStatus(),
                    "sotr" => $application->getUser()->getLastname()." ".$application->getUser()->getFirstname()." ".$application->getUser()->getMiddlename()
                ];
            }
            else
            {
                $apps[] = [
                    "id" => $application->getId(),
                    "title" => $application->getTitle(),
                    "client" => $application->getClient()->getLastname()." ". $application->getClient()->getFirstname()." ".$application->getClient()->getMiddlename(),
                    "date_create" => $application->getDateCreate()->format("d.m.Y"),
                    "data_process" => "-",
                    "status" => $application->getStatus(),
                    "sotr" => "-"
                ];
            }
        }

        $sheet->fromArray($apps,null, 'A3');
        $counts = count($apps) + 2;
        $sheet->getStyle('A1:G'.$counts)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:G'.$counts)->getFont()->setName('Times New Romans');
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->setTitle("Список заявок");

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Заявки.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/{id}", name="application_show", methods={"GET"})
     */
    public function show(Applications $application): Response
    {
        /*Отображение данных выбранной заявки*/
        return $this->render('application/show.html.twig', [
            'application' => $application,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="application_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Applications $application, \Swift_Mailer $mailer): Response
    {
        /*Редактирование выбранной заявки*/
        if ($request->isMethod('POST'))
        {
            $date = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
            $date->setTimeZone(new \DateTimeZone('Europe/Moscow'));
            $application->setStatus($request->request->get('status'));
            $application->setDateProcess($date);
            $application->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($application);
            $entityManager->flush();

            //Отправка писем на почту
            $message = (new \Swift_Message('Заявка на выполнение услуги: "'.$application->getTitle().'" - ООО РУФОРМ'))
                ->setFrom($application->getUser()->getEmail())
                ->setTo($application->getClient()->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/application.html.twig',
                        ['application' => $application, 'content' => $request->request->get('content')]
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);
            return $this->redirectToRoute('application_show', ['id' => $application->getId()]);
        }
        return $this->render('application/edit.html.twig', [
            'application' => $application,
        ]);
    }

    /**
     * @Route("/{id}", name="application_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Applications $application): Response
    {
        /*Удаление выбранной заявки*/
        if ($this->isCsrfTokenValid('delete'.$application->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($application);
            $entityManager->flush();
        }
        return $this->redirectToRoute('applications_index');
    }
}
