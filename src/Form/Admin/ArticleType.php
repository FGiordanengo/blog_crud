<?php

namespace App\Form\Admin;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'placeholder' => 'Choisir une catégorie',
                'label' => 'form.category', //Ici form.category est défini pour l'exemple, on pourrait utiliser directement Category dans messages.fr.yaml
            ])
            ->add('content', TextareaType::class, [
                'label' => 'form.content'
            ])
            ->add('file', FileType::class, [
                'required' => is_null($builder->getData()->getId()),
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'label' => 'Public ?'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'validation_groups' => function(FormInterface $form) {

                if ($form->getData()->getId() === null) {
                    return ['Default', 'create']; 
                } 
                return ['Default'];
            }
        ]);
    }
}
