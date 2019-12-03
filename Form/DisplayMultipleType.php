<?php

namespace W3com\HulkBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use W3com\HulkBundle\Model\Filter;

class DisplayMultipleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {

            /** @var Filter $data */
            $filter = $formEvent->getData();
            $form = $formEvent->getForm();
            $options = $formEvent->getForm()->getConfig()->getOptions();

            if ($options['filter_type'] === Filter::TYPE_MULTIPLE) {
                $form->add('min', NumberType::class, [
                    'label' => $filter->getLabel(),
                    'required' => false, 'mapped' => false
                ]);

                $form->add('max', NumberType::class, [
                    'label' => false, 'required' => false, 'mapped' => false
                ]);
            } elseif ($options['filter_type'] === Filter::TYPE_MULTIPLE_DATE) {

                $form->add('min', DateType::class, [
                    'format' => 'd/m/Y',
                    'label' => $filter->getLabel(), 'widget' => 'single_text',
                    'html5' => false, 'required' => false, 'mapped' => false
                ]);

                $form->add('max', DateType::class, [
                    'format' => 'd/m/Y',
                    'label' => false, 'widget' => 'single_text',
                    'html5' => false, 'required' => false, 'mapped' => false
                ]);
            }

        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'filter_type' => null,
            'label' => false
        ]);
    }


}