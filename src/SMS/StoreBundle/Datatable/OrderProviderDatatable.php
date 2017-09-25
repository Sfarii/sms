<?php

namespace SMS\StoreBundle\Datatable;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class OrderLineDatatable
 *
 * @package SMS\StoreBundle\Datatables
 */
class OrderProviderDatatable extends AbstractDatatableView
{
    /**
     * @var String Class Names
     */
    protected $providerClass;

    /**
     * provider class
     *
     * @param String Class Names
     */
    function setProviderClass( $providerClass)
    {
        $this->providerClass = $providerClass;
    }
    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $establishment = $this->securityToken->getToken()->getUser()->getEstablishment();

        $provider = $this->em->getRepository($this->providerClass)->findBy(array("establishment" => $establishment));
        
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
            'url' => $this->router->generate('orderprovider_results'),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'actions' => array(
                    array(
                        'route' => 'orderprovider_bulk_delete',
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

            ->add('created', 'datetime', array(
                'title' => $this->translator->trans('orderprovider.field.created'),
                'date_format' => "LL",
                'filter' => array('daterange', array('class' => "md-input")),
            ))
            ->add('reference', 'column', array(
                'title' => $this->translator->trans('orderprovider.field.reference'),
                'filter' => array('text', array(
                    'class' => "md-input"
                ))
            ))
            ->add('provider.socialReason', 'column', array(
                'title' => $this->translator->trans('orderprovider.field.provider'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => $this->translator->trans('filter.field.all')) + $this->getCollectionAsOptionsArray($provider, 'socialReason', 'socialReason'),
                    'class' => "tablesorter-filter"
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
                        'route' => 'orderprovider_show',
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
                        'route' => 'orderprovider_edit',
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
                        'route' => 'order_provider_pdf',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'icon' => '&#xE8AD;',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.show'),
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
        return 'SMS\StoreBundle\Entity\OrderProvider';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orderline_datatable';
    }
}
