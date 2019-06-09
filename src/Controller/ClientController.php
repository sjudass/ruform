<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * @Route("/admin/clients")
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/", name="clients_index", methods={"GET"})
     */
    public function index(ClientRepository $clientRepository): Response
    {
        /*Отображение страницы со списком клиентов*/
        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }

    /**
     * @Route("/export", name="client_export")
     */
    public function export(ClientRepository $clientRepository)
    {
        /*Выгрузка в Excel списка клиентов*/
        $clients = $clientRepository->findAll();
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'Список клиентов');
        $sheet->getStyle('A1')->getFont()->setSize(16);
        $sheet->setCellValue('A2', '№');
        $sheet->setCellValue('B2', 'ФИО');
        $sheet->setCellValue('C2', 'Электронная почта');
        $sheet->setCellValue('D2', 'Номер телефона');
        $sheet->setCellValue('E2', 'Дата обращения');
        $sheet->getStyle('A2:G2')->getFont()->setSize(14);

        foreach ($clients as $client)
        {
            $client_list[] = [
                "id" => $client->getId(),
                "client" => $client->getLastname()." ". $client->getFirstname()." ".$client->getMiddlename(),
                "email" => $client->getEmail(),
                "phone" => $client->getPhone(),
                "data_application" => $client->getDateApplication()->format("d.m.Y"),
            ];
        }

        $sheet->fromArray($client_list,null, 'A3');
        $counts = count($client_list) + 2;
        $sheet->getStyle('A1:G'.$counts)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:G'.$counts)->getFont()->setName('Times New Romans');
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->setTitle("Список клиентов");

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Клиенты.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }


    /**
     * @Route("/{id}", name="client_show", methods={"GET"})
     */
    public function show(Client $client): Response
    {
        /*Отображение данных выбранного клиента*/
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    /**
     * @Route("/{id}", name="client_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Client $client): Response
    {
        /*Удаление выбранного клиента*/
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($client);
            $entityManager->flush();
        }
        return $this->redirectToRoute('clients_index');
    }
}
