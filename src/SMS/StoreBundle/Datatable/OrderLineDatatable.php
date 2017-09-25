<?php

namespace SMS\StoreBundle\Datatable;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class OrderLineDatatable
 *
 * @package SMS\StoreBundle\Datatable
 */
class OrderLineDatatable extends AbstractDatatableView
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
        //die(var_dump($options['id']));
        $this->ajax->set(array(
            'url' => $this->router->generate('order_line_results', array('id'=> $options['id'])),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'actions' => array(
                    array(
                        'route' => 'order_line_bulk_delete',
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
            ->add('product.sku', 'column', array(
                'title' => $this->translator->trans('product.field.sku'),
                'filter' => array('text', array(
                    'class' => "md-input"
                ))
            ))
            ->add('product.productName', 'column', array(
                'title' => $this->translator->trans('product.field.productName'),
                'filter' => array('text', array(
                    'class' => "md-input"
                )),
            ))
            ->add('price', 'column', array(
                'title' => $this->translator->trans('product.field.price'),
                'filter' => array('text', array(
                    'class' => "md-input"
                )),
                "render" => $this->translator->trans('store.unit.price')
            ))
            ->add('quantity', 'column', array(
                'title' => $this->translator->trans('product.field.quantity'),
                'filter' => array('text', array(
                    'class' => "md-input"
                )),
                "render" => $this->translator->trans('store.unit.elem')
            ))
            ->add('state', 'boolean', array(
                'title' => $this->translator->trans('purchase.field.state'),
                'false_label' => $this->translator->trans('store.state.padding'),
                'true_label' => $this->translator->trans('out_of_stock')
            ))
            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
                    array(
                        'route' => 'product_show',
                        'route_parameters' => array(
                            'id' => 'product.id'
                        ),
                        'icon' => '&#xE8F4;',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.show'),
                        ),
                    ),
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'SMS\StoreBundle\Entity\OrderLine';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order_line_datatable';
    }
}
