<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Entity\Ingredients;
use App\Entity\DetailsRecette;

use App\Form\SearchIngredientType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




class RecetteController extends AbstractController
{
    //l'index va me permettre de voir mes recettes
    //récuperer le user!!!! 
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'recette_index')]
    public function index(RecetteRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        //je récupère toutes les recettes
        $recettes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            5
        );

        //je les envois à la vue
        return $this->render('recette/index.html.twig', [
            'recettes' => $recettes
        ]);
    }

    #[Route('/recette/public', name: 'recette_index_public')]
    public function indexPublic(RecetteRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        //je récupère toutes les recettes
        $recettes = $paginator->paginate(
            $repository->findPublicRecipe(null),
            $request->query->getInt('page', 1),
            5
        );

        //je les envois à la vue
        return $this->render('recette/indexPublic.html.twig', [
            'recettes' => $recettes
        ]);
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/recette/creation', name: 'recette_creation')]
    public function new(Request $request, EntityManagerInterface $manage): Response
    {
        //créer une nouvelle recette vide

        $recette = new Recette();

        // $details1 = new DetailsRecette();
        // $details1->setQuantite('12');
        // $details1->setMesure('gr');
        // $ing = new Ingredients();
        // $ing->setNom ('rizzzz');
        // $manage->persist($ing);
        // $details1->setIngredients($ing);

        // $recette->getDetails()->add($details1);

        // $details2 = new DetailsRecette();
        // $details2->setQuantite('5');
        // $details2->setMesure('ml');
        // $ing2 = new Ingredients();
        // $ing2->setNom ('rizzzz');
        // $manage->persist($ing2);
        // $details1->setIngredients($ing2);


        // $recette->getDetails()->add($details2);

        //rajouter cette recette vide dans le formulaire
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
    
            $recette->setUser($this->getUser());
            $manage->persist($recette);
            $manage->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créé avec succès !'
            );
            return $this->redirectToRoute('recette_index');
        }

            
        $formSearch = $this->createForm(SearchIngredientType::class);

        return $this->render('recette/new.html.twig', [
            'form' => $form->createView(),
            'formSearch' => $formSearch->createView(),
              
        ]);
    }

    #[Security("is_granted('ROLE_USER') and user === recette.getUser()")]
    #[Route('/recette/edition/{id}', 'recette.edit', methods: ['GET', 'POST'])]
    public function edit(
        Recette $recette,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recette = $form->getData();
            $manager->persist($recette);

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifié avec succès !'
            );



            return $this->redirectToRoute('recette_index');
        }

        return $this->render('recette/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/recette/suppression/{id}', 'recette.delete', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER') and user === recette.getUser()")]
    public function delete(
        EntityManagerInterface $manager,
        Recette $recette
    ): Response {
        $manager->remove($recette);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimé avec succès !'
        );

        return $this->redirectToRoute('recette_index');
    }

    #[Route('/recette/{id}', 'recette.show', methods: ['GET', 'POST'])]
    public function show(Recette $recette): Response
    {
        return $this->render(
            'recette/show.html.twig',
            ['recette' => $recette]
        );
    }

}
