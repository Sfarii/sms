<?php

namespace SMS\UserSpaceBundle\UserSpaceDatatable;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class SanctionStudentDatatable
 *
 * @package SMS\UserSpaceBundle\UserSpaceDatatable
 */
class SanctionStudentDatatable extends AbstractDatatableView
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {

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
            'url' => $this->router->generate('sanction_student_results'),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
            ->add('punishment', 'column', array(
                'title' => $this->translator->trans('sanction.field.punishment'),
            ))
            ->add('cause', 'column', array(
                'title' => $this->translator->trans('sanction.field.cause'),
            ))
            ->add('student.firstName', 'column', array(
                'title' => $this->translator->trans('student_sanction.field.firstName'),
            ))
            ->add('student.lastName', 'column', array(
                'title' => $this->translator->trans('student_sanction.field.lastName'),
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
