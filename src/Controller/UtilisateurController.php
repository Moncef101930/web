<?php
namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Knp\Component\Pager\PaginatorInterface; 
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/utilisateur')]
class UtilisateurController extends AbstractController
{
    #[Route('/login', name: 'app_utilisateur_login', methods: ['GET','POST'])]
    public function login(AuthenticationUtils $authUtils): Response
    {
        return $this->render('utilisateur/login.html.twig', [
            'last_username' => $authUtils->getLastUsername(),
            'error'         => $authUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'app_utilisateur_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \Exception('Don’t call this method directly.');
    }

    #[Route('/register', name: 'app_utilisateur_register')]
    public function register(
        Request                      $request,
        UserPasswordHasherInterface  $passwordHasher,
        EntityManagerInterface       $em,
        SluggerInterface             $slugger
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user, [
            'include_admin'     => false,
            'password_required' => true,
            'captcha_enabled'   => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $orig = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safe = $slugger->slug($orig);
                $new  = sprintf('%s-%s.%s', $safe, uniqid(), $imageFile->guessExtension());
                try {
                    $imageFile->move($this->getParameter('images_directory'), $new);
                    $user->setImage($new);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }
            }

            // hash password
            $user->setMotDePasse(
                $passwordHasher->hashPassword($user, $user->getMotDePasse())
            );

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_utilisateur_login');
        }

        return $this->render('utilisateur/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile', name: 'app_utilisateur_profile', methods: ['GET','POST'])]
    public function profile(
        Request                      $request,
        EntityManagerInterface       $em,
        UserPasswordHasherInterface  $passwordHasher,
        SluggerInterface             $slugger
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(UtilisateurType::class, $user, [
            'include_admin'     => false,
            'password_required' => false, // now unmapped
            'captcha_enabled'   => false,
        ]);
        $form->remove('email');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $orig = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safe = $slugger->slug($orig);
                $new  = sprintf('%s-%s.%s', $safe, uniqid(), $imageFile->guessExtension());
                try {
                    $imageFile->move($this->getParameter('images_directory'), $new);
                    $user->setImage($new);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Échec de l’upload de l’image.');
                }
            }
            // password change if provided
            $plain = $form->get('motDePasse')->getData();
            if ($plain) {
                $user->setMotDePasse(
                    $passwordHasher->hashPassword($user, $plain)
                );
            }

            $em->flush();
            $this->addFlash('success', 'Profil mis à jour.');
            return $this->redirectToRoute('app_utilisateur_profile');
        }

        return $this->render('utilisateur/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        EntityManagerInterface $em
    ): Response {
        $qb = $em->getRepository(Utilisateur::class)
                 ->createQueryBuilder('u');

        // 1) search
        $search = $request->query->get('search', '');
        if ($search !== '') {
            $qb->andWhere('u.nom LIKE :q OR u.prenom LIKE :q OR u.email LIKE :q')
               ->setParameter('q', '%'.$search.'%');
        }

        // 2) sort
        $sort      = $request->query->get('sort', '');
        $direction = $request->query->get('direction', '');
        $allowed   = ['id','nom','prenom','email','role','dateNaissance'];
        if (in_array($sort, $allowed, true) && in_array(strtoupper($direction), ['ASC','DESC'], true)) {
            $qb->orderBy('u.'.$sort, $direction);
        } else {
            $qb->orderBy('u.nom','ASC');
        }

        // 3) paginate
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        // 4) if AJAX: return JSON with two rendered fragments
        if ($request->isXmlHttpRequest()) {
            $table      = $this->renderView('utilisateur/_rows.html.twig', ['pagination' => $pagination]);
            $paginationHtml = $this->renderView('utilisateur/_pagination.html.twig', ['pagination' => $pagination]);

            return new JsonResponse([
                'table'      => $table,
                'pagination' => $paginationHtml,
            ]);
        }

        // 5) initial full render
        return $this->render('utilisateur/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/new', name: 'app_utilisateur_new', methods: ['GET','POST'])]
    public function new(
        Request                     $request,
        EntityManagerInterface      $em,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface            $slugger
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user, [
            'include_admin'     => true,
            'password_required' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // handle image upload (identical to register)…
            if ($img = $form->get('image')->getData()) {
                $orig = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
                $safe = $slugger->slug($orig);
                $new  = sprintf('%s-%s.%s', $safe, uniqid(), $img->guessExtension());
                try {
                    $img->move($this->getParameter('images_directory'), $new);
                    $user->setImage($new);
                } catch (\Exception $e) {
                    $this->addFlash('error','Image upload failed.');
                }
            }
            // hash and set password
            $user->setMotDePasse(
                $passwordHasher->hashPassword($user, $user->getMotDePasse())
            );

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_utilisateur_index');
        }

        return $this->render('utilisateur/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET','POST'])]
    public function edit(
        Request                $request,
        Utilisateur            $utilisateur,
        EntityManagerInterface $em,
        SluggerInterface       $slugger,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $form = $this->createForm(UtilisateurType::class, $utilisateur, [
            'include_admin'     => true,
            'password_required' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // image & password...
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $orig = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safe = $slugger->slug($orig);
                $new  = sprintf('%s-%s.%s', $safe, uniqid(), $imageFile->guessExtension());
                try {
                    $imageFile->move($this->getParameter('images_directory'), $new);
                    $utilisateur->setImage($new);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }
            }
            $utilisateur->setMotDePasse(
                $passwordHasher->hashPassword($utilisateur, $utilisateur->getMotDePasse())
            );

            $em->flush();
            return $this->redirectToRoute('app_utilisateur_index');
        }

        return $this->render('utilisateur/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(
        Request                $request,
        Utilisateur            $utilisateur,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->get('_token'))) {
            $em->remove($utilisateur);
            $em->flush();
        }
        return $this->redirectToRoute('app_utilisateur_index');
    }
}
