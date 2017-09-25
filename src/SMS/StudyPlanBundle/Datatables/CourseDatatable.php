<?php

namespace SMS\StudyPlanBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;
use SMS\UserBundle\Entity\User;
use SMS\UserBundle\Entity\Administrator;
use SMS\EstablishmentBundle\Entity\Grade;
use SMS\EstablishmentBundle\Entity\Division;

/**
 * Class CourseDatatable
 *
 * @package SMS\StudyPlanBundle\Datatables
 */
class CourseDatatable extends AbstractDatatableView
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $establishment = $this->securityToken->getToken()->getUser()->getEstablishment();

        $divisions = $this->em->getRepository(Division::class)->findBy(array("establishment" => $establishment));

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
            'class' => "uk-table uk-table-striped",
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'use_integration_options' => true,
            'row_id' => 'id'
        ));

        $this->ajax->set(array(
            'url' => $this->router->generate('course_results' , array('id' => $options['id'])),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'actions' => array(
                    array(
                        'route' => 'course_bulk_delete',
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
            ->add('courseName', 'column', array(
                'title' => $this->translator->trans('course.field.courseName'),
                'filter' => array('text', array(
                    'class' => "md-input"
                ))
            ))
            ->add('coefficient', 'column', array(
                'title' => $this->translator->trans('course.field.coefficient'),
                'filter' => array('text', array(
                    'class' => "md-input"
                ))
            ))
            ->add('division.divisionName', 'column', array(
                'title' => $this->translator->trans('division.field.divisionName'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => $this->translator->trans('filter.field.all')) + $this->getCollectionAsOptionsArray($divisions, 'divisionName', 'divisionName'),
                    'class' => "tablesorter-filter"
                ))
            ))
            ->add('user.username', 'column', array(
                'title' => $this->translator->trans('author.creator'),
                'filter' => array('text', array(
                    'class' => "md-input"
                ))
            ))
            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
                    array(
                        'route' => 'course_show',
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
                        'route' => 'course_edit',
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
        return 'SMS\StudyPlanBundle\Entity\Course';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'course_datatable';
    }
}
