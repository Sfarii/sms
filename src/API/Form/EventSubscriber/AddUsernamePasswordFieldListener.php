<?php

namespace API\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use API\Form\Type\RoleType;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddUsernamePasswordFieldListener implements EventSubscriberInterface
{
	private $_action ;

	public static function getSubscribedEvents()
	{
		return array(
			FormEvents::PRE_SUBMIT => 'onPreSubmit',
			FormEvents::PRE_SET_DATA => 'preSetData'
			);
	}

	public function preSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if (!$user || null === $user->getId()) {
            $this->_action = 'ADD';
        }else{
        	$this->_action = 'EDIT';
        	$form->remove('show_username_password')
        		->add('enabled' ,CheckboxType::class , array(
					'label' => 'user.field.enabled'
					)
				)->add('roles' , RoleType::class , array(
					'label' => 'user.field.role',
					)
				)->add('save', SubmitType::class ,array(
	                'validation_groups' => "Edit",
	                'label' => 'md-fab'
	            ));
        }
    }

	public function onPreSubmit(FormEvent $event)
	{
		$user = $event->getData();
		$form = $event->getForm();
		if (!$user||!array_key_exists('show_username_password', $user) || $this->_action == 'EDIT'){
			return;
		}
		if (array_key_exists('show_username_password', $user)) {
			
			unset($user['save']);
			
			$form->add('username' ,TextType::class , array(
				'label' => 'user.field.username'
					)
				)
				->add('plainPassword',PasswordType::class ,array(
				'label' => 'user.field.password' 
					)
				)->add('save', SubmitType::class ,array(
                'validation_groups' => "Registration",
                'label' => 'md-fab'
            ));
		} else {
			unset($user['save']);
			$form->add('save', SubmitType::class ,array(
                'validation_groups' => "SimpleRegistration",
                'label' => 'md-fab'
            ));
			
			unset($user['username']);
			unset($user['plainPassword']);
			$event->setData($user);
		}
	}
}