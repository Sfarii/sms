<?php

namespace SMS\SchoolBundle\Controller;

use API\BaseController\BaseController;
use SMS\SchoolBundle\Entity\Contact;
use SMS\SchoolBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use SMS\SchoolBundle\Entity\Feature;
use SMS\SchoolBundle\Entity\Pricing;
use SMS\SchoolBundle\Entity\SchoolTestimonial;
use SMS\SchoolBundle\Entity\Slider;
use SMS\SchoolBundle\Entity\AboutUs;

class DefaultController extends BaseController
{
    /**
     * @Route("/translate/{lang}" , name="translate_index")
     * @Method("GET")
     */
    public function translatorAction($lang)
    {
        $session = $this->getRequest()->getSession();
        $session->set('_locale', $lang);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * @Route("/" , name="home_page")
     * @Method("GET")
     * @Template("SMSSchoolBundle:default:index.html.twig")
     */

    public function indexAction()
    {
      $testimonials = $this->getDoctrine()->getRepository(SchoolTestimonial::class)->findAll();
      $sliders = $this->getDoctrine()->getRepository(Slider::class)->findAll();
      $aboutus = $this->getDoctrine()->getRepository(AboutUs::class)->findAll();
      $pricings = $this->getDoctrine()->getRepository(Pricing::class)->findAll();
      $features = $this->getDoctrine()->getRepository(Feature::class)->findAll();

      return array("sliders" => $sliders , "features" => $features ,"testimonials" => $testimonials ,"aboutus" => $aboutus,"pricings" => $pricings );
    }

    /**
     * @Route("/contact_us" , name="contact_page")
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:default:contact.html.twig")
     */

    public function contactAction(Request $request)
    {
      $contact = new Contact();
      $form = $this->createForm(ContactType::class, $contact);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $this->getEntityManager()->insert($contact);
          $this->flashSuccessMsg('contact.add.success');
          return $this->redirectToRoute('contact_page');
      }

      return array(
          'contact' => $contact,
          'form' => $form->createView(),
      );
    }
}
