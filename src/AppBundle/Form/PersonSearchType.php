<?php

namespace AppBundle\Form;

use AppBundle\Entity\Format;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of TitleSearchType
 */
class PersonSearchType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->setMethod('get');
        $em = $options['entity_manager'];
        
        $formats = $em->getRepository(Format::class)->findAll(array(
            'name' => 'ASC',
        ));
        $genres = $em->getRepository(Genre::class)->findAll(array(
            'name' => 'ASC',
        ));

        $builder->add('name', TextType::class, array(
            'label' => 'Name',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter all or part of a perosnal name.'
            ),
        ));
        
        $builder->add('gender', ChoiceType::class, array(
            'label' => 'Gender',
            'choices' => array(
                'Female' => 'F',
                'Male' => 'M',
                '(unknown)' => 'U',
            ),
            'attr' => array(
                'group_class' => 'hidden secondary',
                'help_block' => 'Leave this field blank to include all genders.'
            ),
            'required' => false,
            'expanded' => true,
            'multiple' => true,
            'empty_data' => null,
            'data' => null,
        ));

        $builder->add('dob', TextType::class, array(
            'label' => 'Birth Year',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter a year (eg <kbd>1795</kbd>) or range of years (<kbd>1790-1800</kbd>) or a partial range of years (<kbd>*-1800</kbd>).',
                'group_class' => 'hidden secondary',
            ),
        ));
        
        $builder->add('dod', TextType::class, array(
            'label' => 'Death Year',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter a year (eg <kbd>1795</kbd>) or range of years (<kbd>1790-1800</kbd>) or a partial range of years (<kbd>*-1800</kbd>).',
                'group_class' => 'hidden secondary',
            ),
        ));

        $builder->add('birthplace', TextType::class, array(
            'label' => 'Birth Place',
            'required' => false,
            'attr' => array(
                'group_class' => 'hidden secondary',
            ),
        ));
        
        $builder->add('deathplace', TextType::class, array(
            'label' => 'Death Place',
            'required' => false,
            'attr' => array(
                'group_class' => 'hidden secondary',
            ),
        ));
        
        
        $builder->add('orderby', ChoiceType::class, array(
            'label' => 'Order by',
            'choices' => array(
                'Last name' => 'lastname',
                'First name' => 'firstname',
                'Birth date' => 'dob',
                'Death date' => 'dod',
            ),
            'attr' => array(
                'group_class' => 'hidden secondary',
            ),
            'required' => true,
            'expanded' => true,
            'multiple' => false,
            'empty_data' => 'lastname',
            'data' => 'lastname',
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