<?php

namespace App\Form;

use App\Entity\Ingredients;
use App\Entity\DetailsRecette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\DataTransformer\IngredientsTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class DetailsRecetteType extends AbstractType
{

    private IngredientsTransformer $ingTr;

    public function __construct(IngredientsTransformer $ingTr)
    {
        $this->ingTr = $ingTr;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder
            ->add('quantite')
            ->add('mesure')
            ->add(
                'ingredients',
                HiddenType::class,
                [
                    'attr' => [
                        'class' => 'hidden_detail_ingredient',
                    ]
                ]
            )
            ->get('ingredients')
            ->addModelTransformer($this->ingTr);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DetailsRecette::class,
        ]);
    }
}
