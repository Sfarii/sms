<?php
namespace SMS\SchoolBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SMS\SchoolBundle\Form\EventSubscriber\AddTranslatedFieldSubscriber;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;

class TranslatedFieldType extends AbstractType
{
    protected $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(! class_exists($options['personal_translation']))
        {
            Throw new \InvalidArgumentException(sprintf("Unable to find personal translation class: '%s'", $options['personal_translation']));
        }
        if(! $options['field'])
        {
            Throw new \InvalidArgumentException("You should provide a field to translate");
        }
        $subscriber = new AddTranslatedFieldSubscriber($builder->getFormFactory(), $this->container, $options);
        $builder->addEventSubscriber($subscriber);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults($this->getDefaultOptions());
    }
    public function getDefaultOptions(array $options = array())
    {
        $options['remove_empty'] = true;
        $options['csrf_protection'] = false;
        $options['personal_translation'] = false;
        $options['locales'] = array('en', 'fr');
        $options['required_locale'] = array('en', 'fr');
        $options['default_locale'] = $this->container->get('session')->get('_locale' , 'en');
        $options['field'] = false;
        $options['widget'] = "text";
        $options['entity_manager_removal'] = true;
        $options['attr'] = [];
        return $options;
    }
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locales'] = $options['locales'];
        $view->vars['default_locale'] = $options['required_locale'];
        $view->vars['field'] = $options['field'];
    }
    public function getName()
    {
        return 'sms_translatable_field';
    }
}
