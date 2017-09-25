<?php

namespace SMS\StoreBundle\Datatable;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class OrderUserDatatable
 *
 * @package SMS\StoreBundle\Datatables
 */
class OrderUserDatatable extends AbstractDatatableView
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
            'url' => $this->router->generate('orderuser_results'),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'actions' => array(
                    array(
                        'route' => 'orderuser_bulk_delete',
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
            ->add('orderDate', 'datetime', array(
                'title' => $this->translator->trans('orderuser.field.orderDate'),
                'date_format' => "LL",
                'filter' => array('daterange', array('class' => "md-input")),
            ))
            ->add('reference', 'column', array(
                'title' => $this->translator->trans('orderuser.field.reference'),
                'filter' => array('text', array(
                    'class' => "md-input"
                ))
            ))
            ->add('state', 'boolean', array(
                'title' => $this->translator->trans('orderuser.field.state'),
                'true_label' => $this->translator->trans('out_of_stock'),
                'false_label' => $this->translator->trans('store.state.padding'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => $this->translator->trans('filter.field.all') , true => $this->translator->trans('out_of_stock') , false => $this->translator->trans('store.state.padding')) ,
                    'class' => "tablesorter-filter"
                ))
            ))
            ->add('userOrder.username', 'column', array(
                'title' => $this->translator->trans('orderuser.field.userorder'),
                'filter' => array('text', array(
                    'class' => "md-input"
                ))
            ))

            ->add('author.username', 'column', array(
                'title' => $this->translator->trans('author.creator'),
                'filter' => array('text', array(
                    'class' => "md-input"
                ))
            ))

            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
                    array(
                        'route' => 'orderuser_show',
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
                        'route' => 'orderuser_edit',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'icon' => '&#xE150;',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.edit'),
                        ),
                    ),
                    array(
                        'route' => 'order_user_pdf',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'icon' => '&#xE8AD;',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.pdf'),
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
        return 'SMS\StoreBundle\Entity\OrderUser';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orderuser_datatable';
    }
}
