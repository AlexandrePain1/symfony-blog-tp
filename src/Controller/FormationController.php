<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/formation')]
class FormationController extends AbstractController
{

    #[Route('/pdf/{id}', name: 'app_formation_pdf', methods: ['GET'])]
    public function pdf(Formation $formation): Response
    {
        $pdf = new \TCPDF();

        $pdf->SetAuthor('SIO TEAM ! 💻');
        $pdf->SetTitle($formation->getName());
        $pdf->SetFont('times', '', 14);
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->setCellMargins(1, 1, 1, 1);

        $pdf->AddPage();

        
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetFillColor(160,222,255);
        $pdf->SetTextColor(0, 63,144);
        $pdf->MultiCell(187, 20, "PROGRAMME DE FORMATION", 0, 'C', 1, 1, '', '', true, 0, false, true, 20, 'M');

        $pdf->SetFont('helvetica', 'B', 17);
        $pdf->SetFillColor(225,225,230);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(187, 10, $formation->getName(), 0, 'C', 1, 1, '', '', true);
        
        $pdf->setCellPaddings(3,3,3,3);
        $textg = '
        <style> .blue { color: rgb(0, 63,144); } .link { color: rgb(100,0,0); }</style>
        <br>
        <p class="blue">
<b>Tarifs :</b></p><p>
'. $formation->getPrice() .' € net.
        </p><br>
        <p class="blue">
<b>Modalités :</b>
        </p><p>
Formation individuelle<br>
2 journées de formation en présentiel<br>
14 heures (2x7 heures)
        </p><br>
        <p class="blue">
<b>Contact :</b>
        </p><p>
<b>Alexia HEBERT, responsable de FCPRO</b><br>
Service de Formation Professionnelle<br>
Continue de l’OGEC Notre Dame de la Providence<br>
<br>
9, rue chanoine Bérenger BP 340, 50300 AVRANCHES.<br>
Tel 02 33 58 02 22<br>
mail : <span class="link">fcpro@ndlpavranches.fr</span><br>
<br>
N° activité 25500040250<br>
OF certifié QUALIOPI pour les actions de formations<br>
<br>
Site Web : <span class="link">https://ndlpavranches.fr/fc-pro/</span><br>
        </p>';

        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetFillColor(225,225,230);
        $pdf->writeHTMLCell(65, 230, "", "", $textg, 0, 0, 1, true, '', true);

        $textd = '
        <style>hr { color: rgb(0, 63,144); }</style>
        <p><b>Objectif de la formation</b>
        <hr>
        <ul><li>Objectif...</li><li>Objectif...</li><li>Objectif...</li></ul>
        <b>Prérequis necessaire / public visé</b>
        <hr>
        <ul><li>Prérequis...</li><li>Prérequis...</li></ul>
        </p>';

        $pdf->SetFont('helvetica', '', 14);
        $pdf->SetFillColor(255,255,255);
        $pdf->writeHTMLCell(120, 230, "", "", $textd, 0, 0, 1, true, '', true);

        return $pdf->Output('fcpro-formation-' . $formation->getId() . '.pdf','I');
    }

    #[Route('/catalog', name: 'app_formation_catalog', methods: ['GET'])]
    public function catalog(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/catalog.html.twig', [
            'formations' => $formationRepository->findAllInTheFuture(),
        ]);
    }
    
    #[Route('/', name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $formation = new Formation();
        $formation->setCreatedAt(new DateTimeImmutable());
        $formation->setCreatedBy($this->getUser());
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formationRepository->save($formation, true);

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formationRepository->save($formation, true);

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $formationRepository->remove($formation, true);
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}
