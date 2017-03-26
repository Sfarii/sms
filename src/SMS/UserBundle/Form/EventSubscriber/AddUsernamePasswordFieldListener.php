<?php

namespace SMS\UserBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
        	$form->remove('show_username_password');
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
            ));
		} else {
			unset($user['save']);
			$form->add('save', SubmitType::class ,array(
                'validation_groups' => "SimpleRegistration",
            ));
			
			unset($user['username']);
			unset($user['plainPassword']);
			$event->setData($user);
		}
	}
}