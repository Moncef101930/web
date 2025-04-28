<?php
namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\evenement;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    #[Route('/ticket/new/{id}', name: 'app_ticket_new')]
    public function new(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_utilisateur_login');
        }

        $evenement = $em->getRepository(Evenement::class)->find($id);
        if (!$evenement) {
            throw $this->createNotFoundException('Événement introuvable');
        }

        $ticket = new Ticket();
        $form   = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket
                ->setUtilisateurNom($user->getPrenom().' '.$user->getNom())
                ->setEvenementNom($evenement->getNom())
                ->setDateAchat(new \DateTime())
            ;
            $em->persist($ticket);
            $em->flush();

            $this->addFlash('success', 'Ticket acheté avec succès.');
            return $this->redirectToRoute('app_ticket_index');
        }

        return $this->render('ticket/new.html.twig', [
            'form'       => $form->createView(),
            'evenement'  => $evenement,
        ]);
    }

    #[Route('/ticket', name: 'app_ticket_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_utilisateur_login');
        }

        $tickets = $em->getRepository(Ticket::class)
                     ->findBy(['utilisateurNom' => $user->getPrenom().' '.$user->getNom()]);

        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }
}
