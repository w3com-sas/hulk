<?php

namespace W3com\HulkBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use W3com\BoomBundle\Generator\Model\Entity;
use W3com\BoomBundle\Generator\Model\Property;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Filter;

class DisplayType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {

            /** @var Display $display */
            $display = $formEvent->getData();
            $form = $formEvent->getForm();

            $filenameType = !empty($display->getDisplayNames()) ? ChoiceType::class : HiddenType::class;
            $filenameOptions = !empty($display->getDisplayNames()) ? ['choices' => $display->getDisplayNames(), 'label' => 'Display'] : [];
            $form->add('filename', $filenameType, $filenameOptions);

            /** @var Property $globalSearchProperty */
            $globalSearchProperty = $display->getSearchProperty();

            if ($globalSearchProperty !== null){
                $form->add($globalSearchProperty->getField(), TextType::class, [
                    'mapped' => false, 'label' => 'Recherche générale', 'required' => false
                ]);
            }

            /** @var Filter $filter */
            foreach ($display->getFilters() as $filter) {

                if ($filter->getType() === Filter::TYPE_MULTIPLE_DATE) {

                    $form->add('_interval' . $filter->getFieldName(), DisplayMultipleType::class, [
                        'data' => $filter, 'filter_type' => Filter::TYPE_MULTIPLE_DATE, 'mapped' => false
                    ]);

                } elseif ($filter->getType() === Filter::TYPE_MULTIPLE) {

                    $form->add('_interval' . $filter->getFieldName(), DisplayMultipleType::class, [
                        'data' => $filter, 'filter_type' => Filter::TYPE_MULTIPLE, 'mapped' => false]);

                } elseif ($filter->getType() === Filter::TYPE_SINGLE) {
                    $form->add($filter->getFieldName(), ChoiceType::class, ['mapped' => false,
                        'label' => $filter->getLabel(), 'choices' => $filter->getValues(), 'required' => false,
                        'attr' => ['onchange' => 'reloadDisplayForm()']]);

                } elseif ($filter->getType() === Filter::TYPE_DATE) {

                    $form->add($filter->getFieldName(), DateType::class, [
                        'format' => 'd/m/Y',
                        'label' => $filter->getLabel(), 'mapped' => false, 'widget' => 'single_text',
                        'required' => false, 'html5' => false
                    ]);
                }
            }
            $form->add('submit', SubmitType::class, ['label' => 'Rechercher', 'attr' =>
                ['class' => 'btn btn-blue btn-block']])->add('calcView', HiddenType::class);

        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Display::class,
            'label' => false
        ]);
    }

}