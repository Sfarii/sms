<?php

namespace SMS\AdministrativeBundle\Datatable;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;
use DatatablesBundle\DataTable\AttendanceColumn;

/**
 * Class AttendanceDatatable
 *
 * @package SMS\AdministrativeBundle\Datatable
 */
class StudentAttendanceDatatable extends AbstractDatatableView
{
    /**
     * @var String Class Names
     */
    protected $status;
    protected $divisionClass;
    protected $sessionClass;

    /**
     * Session class
     *
     * @param String Class Names
     */
    function setSessionClass( $sessionClass)
    {
        $this->sessionClass = $sessionClass;
    }

    /**
     * Section class
     *
     * @param String Class Names
     */
    function setDivisionClass( $divisionClass)
    {
        $this->divisionClass = $divisionClass;
    }

    /**
     * status
     *
     * @param Status
     */
    public function setStatus($status)
    {
        $this->status = array('' => $this->translator->trans('filter.field.all') );

        foreach ($status as $key => $value) {
          $this->status[$key] = $this->translator->trans($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $establishment = $this->securityToken->getToken()->getUser()->getEstablishment();
        $divisions = $this->em->getRepository($this->divisionClass)->findBy(array("establishment" => $establishment));
        $sessions = $this->em->getRepository($this->sessionClass)->findBy(array("establishment" => $establishment));


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
            'url' => $this->router->generate('attendance_student_info_results' , array('id'=> $options['id'])),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
              ->add(null, 'multiselect', array(
                  'actions' => array(
                      array(
                          'route' => 'attendance_student_bulk_update',
                          'route_parameters' => array(
                              'status' => 'R'
                          ),
                          'icon' => '&#xE923;',
                          'label' => $this->translator->trans('attendance_action.retard'),
                          'attributes' => array(
                              'rel' => 'tooltip',
                              'title' => $this->translator->trans('attendance_action.retard'),
                              'class' => 'md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light',
                              'role' => 'button'
                          ),
                      ),
                      array(
                          'route' => 'attendance_student_bulk_update',
                          'route_parameters' => array(
                              'status' => 'P'
                          ),
                          'icon' => '&#xE923;',
                          'label' => $this->translator->trans('attendance_action.present'),
                          'attributes' => array(
                              'rel' => 'tooltip',
                              'title' => $this->translator->trans('attendance_action.present'),
                              'class' => 'md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light',
                              'role' => 'button'
                          ),
                      ),
                      array(
                          'route' => 'attendance_student_bulk_update',
                          'route_parameters' => array(
                              'status' => 'A'
                          ),
                          'icon' => '&#xE923;',
                          'label' => $this->translator->trans('attendance_action.absent'),
                          'attributes' => array(
                              'rel' => 'tooltip',
                              'title' => $this->translator->trans('attendance_action.absent'),
                              'class' => 'md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light',
                              'role' => 'button'
                          ),
                      ),
                      array(
                          'route' => 'attendance_student_bulk_update',
                          'route_parameters' => array(
                              'status' => 'E'
                          ),
                          'icon' => '&#xE923;',
                          'label' => $this->translator->trans('attendance_action.exclude'),
                          'attributes' => array(
                              'rel' => 'tooltip',
                              'title' => $this->translator->trans('attendance_action.exclude'),
                              'class' => 'md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light',
                              'role' => 'button'
                          ),
                      ),
                  )
              ))
              ->add('attendanceSection.date', 'datetime', array(
                  'title' => $this->translator->trans('attendance_student.field.date'),
                  'date_format' => "LL",
                  'filter' => array('daterange', array('class' => "md-input"))
              ))

              ->add('attendanceSection.course.division.divisionName', 'column', array(
                  'title' => $this->translator->trans('attendance_professor.field.divisionName'),
                  'filter' => array('select', array(
                      'search_type' => 'eq',
                      'select_options' => array('' => $this->translator->trans('filter.field.all')) + $this->getCollectionAsOptionsArray($divisions, 'divisionName', 'divisionName'),
                      'class' => "tablesorter-filter"
                  ))
              ))
              ->add('attendanceSection.session.sessionName', 'column', array(
                  'title' => $this->translator->trans('attendance_student.field.sessionName'),
                  'filter' => array('select', array(
                      'search_type' => 'eq',
                      'select_options' => array('' => $this->translator->trans('filter.field.all')) + $this->getCollectionAsOptionsArray($sessions, 'sessionName', 'sessionName'),
                      'class' => "tablesorter-filter"
                  ))
              ))
              ->add('attendanceSection.course.courseName', 'column', array(
                  'title' => $this->translator->trans('attendance_student.field.courseName'),
                  'filter' => array('text', array(
                      'class' => "md-input"
                  ))
              ))
            ->add('status', 'column', array(
                'title' => $this->translator->trans('attendance_student.field.status'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => $this->status,
                    'class' => "tablesorter-filter"
                )),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'SMS\AdministrativeBundle\Entity\AttendanceStudent';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'attendance_datatable';
    }
}
