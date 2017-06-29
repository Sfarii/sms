<?php

namespace SMS\StoreBundle\StoreDatatable;

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
            'individual_filtering' => false,
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
                            'class' => 'md-btn buttons-copy buttons-html5',
                            'role' => 'button'
                        ),
                    ),
                )
            ))
            ->add('price', 'column', array(
                'title' => $this->translator->trans('orderuser.field.price'),
            ))
            ->add('quantity', 'column', array(
                'title' => $this->translator->trans('orderuser.field.quantity'),
            ))
            ->add('state', 'boolean', array(
                'title' => $this->translator->trans('orderuser.field.state'),
            ))
            ->add('product.productName', 'column', array(
                'title' => $this->translator->trans('product.field.productName'),
            ))
            ->add('student.username', 'column', array(
                'title' => $this->translator->trans('user.field.username'),
            ))

            ->add('user.username', 'column', array(
                'title' => $this->translator->trans('author.creator'),
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
