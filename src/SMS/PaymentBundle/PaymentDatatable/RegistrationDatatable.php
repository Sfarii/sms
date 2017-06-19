<?php

namespace SMS\PaymentBundle\PaymentDatatable;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class RegistrationDatatable
 *
 * @package SMS\PaymentBundle\Datatables
 */
class RegistrationDatatable extends AbstractDatatableView
{
    /**
     * @var String Class Names
     */
    protected $sectionClass;
    protected $registrationTypeClass;

    /**
     * Session class
     *
     * @param String Class Names
     */
    function setRegistrationTypeClass( $registrationTypeClass)
    {
        $this->registrationTypeClass = $registrationTypeClass;
    }

    /**
     * Section class
     *
     * @param String Class Names
     */
    function setSectionClass( $sectionClass)
    {
        $this->sectionClass = $sectionClass;
    }


    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
      $establishment = $this->securityToken->getToken()->getUser()->getEstablishment();
      $sections = $this->em->getRepository($this->sectionClass)->findBy(array("establishment" => $establishment));
      $registrationTypes = $this->em->getRepository($this->registrationTypeClass)->findBy(array("establishment" => $establishment));


        $this->callbacks->set(array(
        'row_callback' => array(
            'template' => 'Pagination/row_callback.js.twig',
            )
        ));


        $this->options->set(array(
            'display_start' => 0,
            'defer_loading' => -1,
            'dom' => "<'dt-uikit-header'<'uk-grid'<'uk-width-medium-2-3'l><'uk-width-medium-1-3'f>>><'uk-overflow-container'tr><'dt-uikit-footer'<'uk-grid'<'uk-width-medium-3-10'i><'uk-width-medium-7-10'p>>>",
            'length_menu' => array(10, 25, 50, 100),
            'order_classes' => true,
            'order' => array(array(1, 'asc')),
            'order_multi' => true,
            'page_length' => 10,
            'paging_type' => Style::FULL_NUMBERS_PAGINATION,
            'renderer' =>  'uikit',
            'scroll_collapse' => false,
            'search_delay' => 0,
            'state_duration' => 7200,
            'class' => "uk-table uk-table-striped",
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'use_integration_options' => true,
            'force_dom' => true,
        ));

        $this->ajax->set(array(
            'url' => $this->router->generate('registration_results'),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'actions' => array(
                    array(
                        'route' => 'registration_bulk_delete',
                        'icon' => '&#xE872;',
                        'label' => $this->translator->trans('action.delete'),
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('action.delete'),
                            'class' => 'md-btn buttons-copy buttons-html5',
                            'role' => 'button'
                        ),
                    ),
                )
            ))
            ->add('student.section.sectionName', 'column', array(
                'title' => $this->translator->trans('attendance_student.field.sectionName'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => $this->translator->trans('filter.field.all')) + $this->getCollectionAsOptionsArray($sections, 'sectionName', 'sectionName'),
                    'class' => "md-input"
                ))
            ))
            ->add('registrationType.registrationTypeName', 'column', array(
                'title' => $this->translator->trans('registrationType.field.registrationTypeName'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => $this->translator->trans('filter.field.all')) + $this->getCollectionAsOptionsArray($registrationTypes, 'registrationTypeName', 'registrationTypeName'),
                    'class' => "md-input"
                ))
            ))
            ->add('registrationType.registrationFee', 'column', array(
                'title' => $this->translator->trans('registrationType.field.registrationFee'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('student.firstName', 'column', array(
                'title' => $this->translator->trans('registration.field.student.firstName'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('student.lastName', 'column', array(
                'title' => $this->translator->trans('registration.field.student.lastName'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('registered', 'boolean', array(
                'title' => $this->translator->trans('registration.field.registered'),
                'true_label' => $this->translator->trans('registration.registered.true_label'),
                'false_label' => $this->translator->trans('registration.registered.false_label'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => $this->translator->trans('filter.field.all') , true => $this->translator->trans('registration.registered.true_label') , false => $this->translator->trans('registration.registered.false_label')) ,
                    'class' => "md-input"
                )),
                'editable' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'SMS\PaymentBundle\Entity\Registration';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'registration_datatable';
    }
}
