<?php

namespace SMS\UserBundle\UserDatatable;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class AdministratorDatatable
 *
 * @package SMS\UserBundle\StudyPlanDatatable
 */
class AdministratorDatatable extends AbstractDatatableView
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
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
            'url' => $this->router->generate('administrator_results'),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'actions' => array(
                    array(
                        'route' => 'user_bulk_deactivate',
                        'icon' => '&#xE14C;',
                        'label' => $this->translator->trans('action.deactivate'),
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('action.delete'),
                            'class' => 'md-btn buttons-copy buttons-html5',
                            'role' => 'button'
                        ),
                    ),
                    array(
                        'route' => 'user_bulk_activate',
                        'icon' => '&#xE876;',
                        'label' => $this->translator->trans('action.activate'),
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('action.delete'),
                            'class' => 'md-btn buttons-copy buttons-html5',
                            'role' => 'button'
                        ),
                    ),
                )
            ))
            ->add('username', 'column', array(
                'title' => $this->translator->trans('user.field.username'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('firstName', 'column', array(
                'title' => $this->translator->trans('administrator.field.firstName'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('lastName', 'column', array(
                'title' => $this->translator->trans('administrator.field.lastName'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('email', 'column', array(
                'title' => $this->translator->trans('user.field.email'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('phone', 'column', array(
                'title' => $this->translator->trans('administrator.field.phone'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))

            ->add('enabled', 'boolean', array(
                'title' => $this->translator->trans('user.field.enabled'),
                'true_label' => $this->translator->trans('user.active.true_label'),
                'false_label' => $this->translator->trans('user.active.false_label'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => $this->translator->trans('filter.field.all') , true => $this->translator->trans('user.active.true_label') , false => $this->translator->trans('user.active.false_label')) ,
                    'class' => "md-input"
                ))
            ))
            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
                    array(
                        'route' => 'administrator_show',
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
                        'route' => 'administrator_edit',
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
        return 'SMS\UserBundle\Entity\Administrator';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'administrator_datatable';
    }
}
