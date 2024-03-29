<?php

namespace App\Form;

use App\Entity\Page;
//use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title');
        /*$builder->add('category', EntityType::class, [
            'class' => Category::class,
            'query_builder' => function (CategoryRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy('u.name', 'ASC');
            },
            'choice_label' => 'name',
            'multiple' => true,
        ]);*/
        $builder->add('text', CKEditorType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
