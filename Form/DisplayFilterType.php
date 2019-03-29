<?php

namespace W3com\HulkBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Filter;

class DisplayFilterType extends AbstractType
{
    private $todayDate;

    public function __construct()
    {
        $dateTime = new \DateTime('now');
        $this->todayDate = $dateTime->format('dd MM yyyy');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent){

            /** @var DataTable $display */
            $display = $formEvent->getData();
            $form = $formEvent->getForm();

            /** @var Filter $filter */
            foreach ($display->getFilters() as $filter){

                if ($filter->getType() === Filter::TYPE_MULTIPLE_DATE) {

                    $form->add('min'.$filter->getFieldName(), DateType::class, [
                        'label' => 'Minimum : '.$filter->getLabel(), 'mapped' => false, 'widget' => 'single_text',
                        'attr' => ['class' => 'form-control mb-2'], 'label_attr' => ['class' => 'input-group-text']

                    ]);

                    $form->add('max'.$filter->getFieldName(), DateType::class, [
                        'label' => 'Minimum : '.$filter->getLabel(), 'mapped' => false, 'widget' => 'single_text',
                        'attr' => ['class' => 'form-control mb-2'], 'label_attr' => ['class' => 'input-group-text']
                    ]);

                } elseif ($filter->getType() === Filter::TYPE_SINGLE) {

                    $form->add($filter->getFieldName(), ChoiceType::class, [ 'mapped' => false,
                        'label' => $filter->getLabel(), 'choices' => $filter->getValues(),
                        'attr' =>
                        ['class' => 'custom-select mb-2'], 'label_attr' => ['class' => 'input-group-text']
                    ]);

                } elseif ($filter->getType() === Filter::TYPE_MULTIPLE) {

                    $form->add('min'.$filter->getFieldName(), NumberType::class, ['mapped' => false,
                        'label' => $filter->getLabel(),
                        'attr' => ['class' => 'form-control mb-2'], 'label_attr' => ['class' => 'input-group-text']
                    ]);

                    $form->add('max'.$filter->getFieldName(), NumberType::class, ['mapped' => false,
                        'label' => $filter->getLabel(),
                        'attr' => ['class' => 'form-control mb-2'], 'label_attr' => ['class' => 'input-group-text']
                    ]);
                }

            }
            $form->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' =>
           ['class' => 'btn btn-success btn-block my-3']]);

        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DataTable::class,
        ]);
    }

}