<?php

namespace App\Form;

use App\Entity\Ingredients;
use App\Entity\DetailsRecette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetailsRecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite')
            ->add('mesure')
            ->add('ingredients', EntityType::class, [
                'class' => Ingredients::class,
                'choice_label' => 'nom',
                'attr' => [
                    
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DetailsRecette::class,
        ]);
    }
}
