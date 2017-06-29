<?php

namespace DatatablesBundle\DataTable;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Sg\DatatablesBundle\Datatable\Column\AbstractColumn;
/**
 * Class ChoiceColumn
 *
 * @package DatatablesBundle\DataTable
 */
class AttendanceColumn extends AbstractColumn
{
    //-------------------------------------------------
    // ColumnInterface
    //-------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        if (empty($data) || !is_string($data)) {
            throw new InvalidArgumentException('setData(): Expecting non-empty string.');
        }

        $this->data = $data;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'DatatablesBundle:Column:choice.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'choices';
    }

    //-------------------------------------------------
    // OptionsInterface
    //-------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'render' => 'render_boolean',
            'filter' => array('select', array(
                'search_type' => 'eq',
                'select_options' => array('' => 'Any', '1' => 'Yes', '0' => 'No')
            )),
            'editable' => false,
            'editable_if' => null
        ));

        $resolver->setAllowedTypes('filter', 'array');
        $resolver->setAllowedTypes('editable', 'bool');
        $resolver->setAllowedTypes('editable_if', array('Closure', 'null'));

        return $this;
    }
}
