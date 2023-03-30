<?php

namespace App\Controller;

use App\Entity\Ingredients;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientsController extends AbstractController
{
    #[Route('/search/ingredients/{texte?}', name: 'search_ingredients')]
    public function searchIngredients(ManagerRegistry $doctrine, Request $req, SerializerInterface $serializer): Response
    {

        if (is_null($req->get('texte'))) {
            $ingredientsJson = "[]";
        } else {
            $ingredients = $doctrine->getManager()->getRepository(Ingredients::class)->createQueryBuilder('i')
                ->where('i.nom LIKE :texte')
                ->setParameter('texte', '%' . $req->get('texte') . '%')
                ->getQuery()
                ->getResult();

            if (count($ingredients) > 0) {
                // si on a trouvé au moins un ingrédient, 
                // on transforme en JSON la réponse en ignorant les rélations (on prend uniquement les données de base de l'ingrédient)
                $ingredientsJson = $serializer->serialize($ingredients, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['details']]);
            } else {
                // pas d'ingrédients trouvé, on envoie du JSON pour un array vide
                $ingredientsJson = "[]";
            }
        }
        return new Response($ingredientsJson);
    }
}
