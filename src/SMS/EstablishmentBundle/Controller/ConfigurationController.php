<?php

namespace SMS\EstablishmentBundle\Controller;

use SMS\EstablishmentBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SMS\EstablishmentBundle\Entity\Establishment;
use SMS\EstablishmentBundle\Form\EstablishmentType;

/**
 * Establishment Configuration.
 *
 * @Route("configuration")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\EstablishmentBundle\Controller
 *
 */
class ConfigurationController extends BaseController
{
    /**
     * Establishment Configuration.
     *
     * @Route("/", name="configuration_options")
     * @Method({"GET", "POST"})
     * @Template("SMSEstablishmentBundle:configuration:index.html.twig")
     */
    public function configAction(Request $request)
    {
      $establishment = $this->getUser()->getEstablishment();
      $editForm = $this->createForm(EstablishmentType::class, $establishment)->handleRequest($request);
      if ($editForm->isSubmitted() && $editForm->isValid()) {
          $this->getEntityManager()->update($establishment);
          $request->getSession()->set('_logo' , $this->get('vich_uploader.templating.helper.uploader_helper')->asset($establishment, 'imageFile') );
          $request->getSession()->set('_theme' , $establishment->getTheme() );
          $this->flashSuccessMsg('establishment.edit.success');
      }

      return array(
          'establishment' => $establishment,
          'form' => $editForm->createView(),
      );
    }
}
