<?php

namespace AppBundle\Form;

use AppBundle\Entity\Format;
use AppBundle\Entity\Genre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of TitleSearchType
 */
class TitleSearchType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->setMethod('get');
        $em = $options['entity_manager'];
        $formats = $em->getRepository(Format::class)->findAll(array(
            'name' => 'ASC',
        ));
        $genres = $em->getRepository(Genre::class)->findAll(array(
            'name' => 'ASC',
        ));

        $builder->add('title', TextType::class, array(
            'label' => 'Title',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter all or part of a title.'
            ),
        ));

        $builder->add('pubdate', TextType::class, array(
            'label' => 'Publication Year',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter a year (eg <kbd>1795</kbd>) or range of years (<kbd>1790-1800</kbd>) or a partial range of years (<kbd>*-1800</kbd>).',
                'group_class' => 'hidden secondary',
            ),
        ));

        $builder->add('format', ChoiceType::class, array(
            'choices' => $formats,
            'choice_label' => function($value, $key, $index) {
                return $value->getName();
            },
            'choice_value' => function($value) {
                return $value->getId();
            },
            'attr' => array(
                'group_class' => 'hidden secondary',
            ),
            'label' => 'Format',
            'required' => false,
            'expanded' => true,
            'multiple' => true,
        ));

        $builder->add('genre', ChoiceType::class, array(
            'choices' => $genres,
            'choice_label' => function($value, $key, $index) {
                return $value->getName();
            },
            'choice_value' => function($value) {
                return $value->getId();
            },
            'attr' => array(
                'group_class' => 'hidden secondary',
            ),
            'label' => 'Genre',
            'required' => false,
            'expanded' => true,
            'multiple' => true,
        ));

        $builder->add('signed_author', TextType::class, array(
            'label' => 'Signed author',
            'required' => false,
            'attr' => array(
                'group_class' => 'hidden secondary',
            ),
        ));


        $builder->add('imprint', TextType::class, array(
            'label' => 'Imprint',
            'required' => false,
            'attr' => array(
                'group_class' => 'hidden secondary',
            ),
        ));

        $builder->add('psuedonym', TextType::class, array(
            'label' => 'Psuedonym',
            'required' => false,
            'attr' => array(
                'group_class' => 'hidden secondary',
            ),
        ));

        $builder->add('orderby', ChoiceType::class, array(
            'label' => 'Order by',
            'choices' => array(
                'Title' => 'title',
                'Publication Date' => 'pubdate',
            ),
            'attr' => array(
                'group_class' => 'hidden secondary',
            ),
            'required' => true,
            'expanded' => true,
            'multiple' => false,
            'empty_data' => 'title',
            'data' => 'title',
        ));
        
        $builder->add('orderdir', ChoiceType::class, array(
            'label' => 'Order Direction',
            'choices' => array(
                'Ascending (A to Z)' => 'asc',
                'Descending (Z to A)' => 'desc',
            ),
            'attr' => array(
                'group_class' => 'hidden secondary',
            ),
            'required' => true,
            'expanded' => true,
            'multiple' => false,
            'empty_data' => 'asc',
            'data' => 'asc',
        ));
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        $resolver->setRequired(array('entity_manager'));
    }

}