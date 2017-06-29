<?php

namespace SMS\PaymentBundle\PaymentDatatable;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class PaymentDatatable
 *
 * @package SMS\PaymentBundle\Datatables
 */
class PaymentDatatable extends AbstractDatatableView
{
    /**
     * @var String Class Names
     */
    protected $sectionClass;
    protected $paymentTypeClass;
    protected $month;

    /**
     * Session class
     *
     * @param String Class Names
     */
    function setPaymentTypeClass( $paymentTypeClass)
    {
        $this->paymentTypeClass = $paymentTypeClass;
    }

    /**
     * month
     *
     * @param Month
     */
    public function setMonth($month)
    {
        $this->month = array('' => $this->translator->trans('filter.field.all') );

        foreach ($month as $key => $value) {
          $this->month[$key] = $this->translator->trans($value);
        }
    }

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
      $typePayments = $this->em->getRepository($this->paymentTypeClass)->findBy(array("establishment" => $establishment));

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
            'url' => $this->router->generate('payment_results'),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'actions' => array(
                    array(
                        'route' => 'payment_bulk_delete',
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
            ->add('student.section.sectionName', 'column', array(
                'title' => $this->translator->trans('attendance_student.field.sectionName'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => $this->translator->trans('filter.field.all')) + $this->getCollectionAsOptionsArray($sections, 'sectionName', 'sectionName'),
                    'class' => "md-input"
                ))
            ))
            ->add('month', 'column', array(
                'title' => $this->translator->trans('payment.field.month'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => $this->month,
                    'class' => "md-input"
                )),
            ))
            ->add('price', 'column', array(
                'title' => $this->translator->trans('payment.field.price'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('credit', 'column', array(
                'title' => $this->translator->trans('payment.field.credit'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('paymentType.TypePaymentName', 'column', array(
                'title' => $this->translator->trans('paymentType.field.TypePaymentName'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => $this->translator->trans('filter.field.all')) + $this->getCollectionAsOptionsArray($typePayments, 'TypePaymentName', 'TypePaymentName'),
                    'class' => "md-input"
                ))
            ))
            ->add('student.firstName', 'column', array(
                'title' => $this->translator->trans('payment.field.student.firstName'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add('student.lastName', 'column', array(
                'title' => $this->translator->trans('payment.field.student.lastName'),
                'filter' => array('text', array(
                    'search_type' => 'eq',
                    'class' => "md-input"
                ))
            ))
            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
                    array(
                        'route' => 'payment_show',
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
                        'route' => 'payment_edit',
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
        return 'SMS\PaymentBundle\Entity\Payment';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'payment_datatable';
    }
}
