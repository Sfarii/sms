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
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {

      $establishment = $this->securityToken->getToken()->getUser()->getEstablishment();
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
            'url' => $this->router->generate('payment_results', array("id" => $options['id'])),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->columnBuilder
            ->add('month', 'column', array(
                'title' => $this->translator->trans('payment.field.month'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => $this->month,
                    'class' => "tablesorter-filter"
                )),
            ))
            ->add('price', 'column', array(
                'title' => $this->translator->trans('payment.field.price'),
                'filter' => array('text', array(
                    'class' => "md-input"
                )),
                "render" => $this->translator->trans('payment.unit.price')
            ))
            ->add('credit', 'column', array(
                'title' => $this->translator->trans('payment.field.credit'),
                'filter' => array('text', array(
                    'class' => "md-input"
                )),
                "render" => $this->translator->trans('payment.unit.price')
            ))
            ->add('paymentType.TypePaymentName', 'column', array(
                'title' => $this->translator->trans('paymentType.field.TypePaymentName'),
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => $this->translator->trans('filter.field.all')) + $this->getCollectionAsOptionsArray($typePayments, 'TypePaymentName', 'TypePaymentName'),
                    'class' => "tablesorter-filter"
                ))
            ))
            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
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
                    ),
                    array(
                        'route' => 'payment_pdf',
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
