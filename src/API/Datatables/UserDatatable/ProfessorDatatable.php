<?php

namespace API\Datatables\UserDatatable;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class ProfessorDatatable
 *
 * @package SMS\UserBundle\Datatables
 */
class ProfessorDatatable extends AbstractDatatableView
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
            'url' => $this->router->generate('professor_results'),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'actions' => array(
                    array(
                        'route' => 'user_bulk_delete',
                        'icon' => '',
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
            ->add('firstName', 'column', array(
                'title' => $this->translator->trans('professor.field.firstName'),
            ))
            ->add('lastName', 'column', array(
                'title' => $this->translator->trans('professor.field.lastName'),
            ))
            ->add('enabled', 'boolean', array(
                'title' => $this->translator->trans('user.field.enabled'),
                'true_label' => $this->translator->trans('user.active.true_label'),
                'false_label' => $this->translator->trans('user.active.false_label'),
            ))
            ->add('birthday', 'datetime', array(
                'title' => $this->translator->trans('professor.field.birthday'),
                'date_format' => "DD/MM/YY"
            ))
            ->add('phone', 'column', array(
                'title' => $this->translator->trans('professor.field.phone'),
            ))
            
            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
                    array(
                        'route' => 'professor_show',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'icon' => '',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.show'),
                        ),
                    ),
                    array(
                        'route' => 'professor_edit',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'icon' => '',
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
        return 'SMS\UserBundle\Entity\Professor';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'professor_datatable';
    }
}
