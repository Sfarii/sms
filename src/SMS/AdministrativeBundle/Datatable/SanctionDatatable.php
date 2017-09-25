<?php

namespace SMS\AdministrativeBundle\Datatable;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;
use SMS\UserBundle\Entity\Administrator;
use SMS\EstablishmentBundle\Entity\Section;

/**
 * Class SanctionDatatable
 *
 * @package SMS\AdministrativeBundle\Datatable
 */
class SanctionDatatable extends AbstractDatatableView
{
    /**
     * @var String Class Names
     */
    protected $studentClass;
    protected $sectionClass;

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
            'url' => $this->router->generate('sanction_results'),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'actions' => array(
                    array(
                        'route' => 'sanction_bulk_delete',
                        'icon' => '&#xE872;',
                        'label' => $this->translator->trans('action.delete'),
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('action.delete'),
                            'class' => 'md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light',
                            'role' => 'button'
                        ),
                    ),
                )
            ))
            ->add('student.section.sectionName', 'column', array(
                'title' => $this->translator->trans('sanction.field.user_sectionName'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => $this->translator->trans('filter.field.all')) + $this->getCollectionAsOptionsArray($sections, 'sectionName', 'sectionName'),
                    'class' => "md-input"
                ))
            ))
            ->add('student.firstName', 'column', array(
                'title' => $this->translator->trans('sanction.field.user_firstName'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('student.lastName', 'column', array(
                'title' => $this->translator->trans('sanction.field.user_lastName'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('punishment', 'column', array(
                'title' => $this->translator->trans('sanction.field.punishment'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
                    array(
                        'route' => 'sanction_show',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'icon' => '&#xE8F4;',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.show'),
                        ),
                    ),
                    array(
                        'route' => 'sanction_edit',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'icon' => '&#xE150;',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.edit'),
                        ),
                    )
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'SMS\AdministrativeBundle\Entity\Sanction';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sanction_datatable';
    }
}
