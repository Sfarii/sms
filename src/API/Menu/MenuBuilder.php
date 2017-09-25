<?php
namespace API\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;
use SMS\UserBundle\Entity\Student;
use SMS\UserBundle\Entity\Manager;
use SMS\UserBundle\Entity\Professor;
use SMS\UserBundle\Entity\StudentParent;
use SMS\UserBundle\Entity\Administrator;

class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @param FactoryInterface $factory
     *@param AuthorizationCheckerInterface $authorizationChecker
     * @param SecurityContext $securityContext
     */
    public function __construct(FactoryInterface $factory , AuthorizationCheckerInterface $authorizationChecker,SecurityContext $securityContext)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->securityContext = $securityContext;
    }

    /**
     * @param array $options
     * @return FactoryInterface
     */
    public function createMainMenu(array $options)
    {
      $user = $this->securityContext->getToken()->getUser();
      if ($user instanceof Student) {
        return $this->studentMenu();
      }elseif ($user instanceof Professor) {
        return $this->professorMenu();
      }elseif ($user instanceof StudentParent) {
        return $this->studentParentMenu();
      }elseif ($user instanceof Administrator) {
        return $this->administratorMenu();
      }elseif ($user instanceof Manager) {
        return $this->ManagerMenu();
      }
      else{
            throw new AccessDeniedException();
      }
    }

    /**
     * set menu for the administrator
     *
     * @param array $options
     * @return FactoryInterface
     */
    public function administratorMenu()
    {
      $menu = $this->factory->createItem('root');

      $menu->addChild('menu.dashbord', array('route' => 'dashbord_index'))
          ->setAttribute('icon', '&#xE871;');

      $menu->addChild('users', array('label' => 'menu.users.title'))
          ->setAttribute('dropdown', true)
          ->setAttribute('icon', '&#xE7EF;');
          $menu['users']->addChild('menu.users.student', array('route' => 'student_index'));
          $menu['users']->addChild('menu.users.parentstudent', array('route' => 'studentparent_index'));
          $menu['users']->addChild('menu.users.professor', array('route' => 'professor_index'));
          $menu['users']->addChild('menu.users.adminstrator', array('route' => 'administrator_index'));

      $menu->addChild('establishment', array('label' => 'menu.establishment.title'))
          ->setAttribute('dropdown', true)
          ->setAttribute('icon', '&#xE80C;');
          $menu['establishment']->addChild('menu.establishment.division', array('route' => 'division_index'));
          $menu['establishment']->addChild('menu.establishment.grade', array('route' => 'grade_index'));
          $menu['establishment']->addChild('menu.establishment.section', array('route' => 'section_index'));

      $menu->addChild('menu.course', array('route' => 'course_index'))
          ->setAttribute('icon', '&#xE865;');

      $menu->addChild('attendance', array('label' => 'menu.administration.attendance'))
          ->setAttribute('dropdown', true)
          ->setAttribute('icon', '&#xE0DF;');
          $menu['attendance']->addChild('menu.administration.attendance_section', array('route' => 'attendance_section_new'));
          $menu['attendance']->addChild('menu.administration.attendance_professor', array('route' => 'attendance_professor_new'));
          $menu['attendance']->addChild('menu.administration.attendance_student', array('route' => 'attendance_student_new'));

      $menu->addChild('schedule', array('label' => 'menu.schedule.title'))
          ->setAttribute('dropdown', true)
          ->setAttribute('icon', '&#xE8B5;');
      $menu['schedule']->addChild('menu.schedule.student', array('route' => 'schedule_student_index'));
      $menu['schedule']->addChild('menu.schedule.professor', array('route' => 'schedule_professor_index'));
      $menu['schedule']->addChild('menu.config_schedule.session', array('route' => 'session_index'));

      $menu->addChild('exam', array('label' => 'menu.exam.title'))
          ->setAttribute('dropdown', true)
          ->setAttribute('icon', '&#xE14F;');
          $menu['exam']->addChild('menu.exam.examType', array('route' => 'typeexam_index'));
          $menu['exam']->addChild('menu.exam.title', array('route' => 'exam_index'));
          $menu['exam']->addChild('menu.exam.student', array('route' => 'exam_students_index'));


      $menu->addChild('payment', array('label' => 'menu.payment.title'))
          ->setAttribute('dropdown', true)
          ->setAttribute('icon', '&#xE53E;');
          $menu['payment']->addChild('menu.payment.payment', array('route' => 'payment_index'));
          $menu['payment']->addChild('menu.payment.paymentType', array('route' => 'paymenttype_index'));
          $menu['payment']->addChild('menu.payment.catchUpLesson', array('route' => 'catchuplesson_index'));
          $menu['payment']->addChild('menu.payment.statistics', array('route' => 'payment_staistics'));

      $menu->addChild('menu.store.title', array('route' => 'shop_index'))
              ->setAttribute('icon', '&#xE8D1;');

      $menu->addChild('store_managment', array('label' => 'menu.store_managment.title'))
          ->setAttribute('dropdown', true)
          ->setAttribute('icon', '&#xE8C9;');
          $menu['store_managment']->addChild('menu.store_managment.provider', array('route' => 'provider_index'));
          $menu['store_managment']->addChild('menu.store_managment.product', array('route' => 'product_index'));
          $menu['store_managment']->addChild('menu.store_managment.product_type', array('route' => 'producttype_index'));
          $menu['store_managment']->addChild('menu.store_managment.delivery', array('route' => 'purchase_index'));
          $menu['store_managment']->addChild('menu.store_managment.order', array('route' => 'orderprovider_index'));
          $menu['store_managment']->addChild('menu.store_managment.order_managment', array('route' => 'orderuser_index'));
          $menu['store_managment']->addChild('menu.store_managment.statistics', array('route' => 'shop_statistics_index'));

      $menu->addChild('administration', array('label' => 'menu.administration.title'))
          ->setAttribute('dropdown', true)
          ->setAttribute('icon', '&#xE0AF;');
          $menu['administration']->addChild('menu.administration.sanction', array('route' => 'sanction_index'));

      $menu->addChild('menu.administration.configuration', array('route' => 'configuration_options'))
              ->setAttribute('icon', '&#xE8B8;');

      return $menu;
    }

    /**
     * set menu for the student
     *
     * @param array $options
     * @return FactoryInterface
     */
    public function studentMenu()
    {
      $menu = $this->factory->createItem('root');

      $menu->addChild('menu.dashbord', array('route' => 'dashbord_index'))
          ->setAttribute('icon', '&#xE871;');

      $menu->addChild('menu.administration.show_attendance', array('route' => 'attendance_student_space'))
          ->setAttribute('icon', '&#xE0DF;');

      $menu->addChild('menu.schedule.show_schedule', array('route' => 'schedule_student_space'))
          ->setAttribute('icon', '&#xE8B5;');

      $menu->addChild('menu.exam.show_exam',  array('route' => 'exam_date_student_space'))
          ->setAttribute('icon', '&#xE14F;');

      $menu->addChild('menu.exam.show_note', array('route' => 'note_student_space'))
          ->setAttribute('icon', '&#xE85D;');

      $menu->addChild('menu.administration.show_sanction', array('route' => 'sanction_student_index'))
          ->setAttribute('icon', '&#xE0AF;');

      return $menu;
    }

    /**
     * set menu for the student
     *
     * @param array $options
     * @return FactoryInterface
     */
    public function ManagerMenu()
    {
      $menu = $this->factory->createItem('root');

      $menu->addChild('menu.dashbord', array('route' => 'dashbord_index'))
          ->setAttribute('icon', '&#xE871;');

      $menu->addChild('menu.establishment.title', array('route' => 'establishment_index'))
          ->setAttribute('icon', '&#xE80C;');

      $menu->addChild('menu.slider', array('route' => 'slider_index'))
          ->setAttribute('icon', '&#xE85D;');

      $menu->addChild('menu.feature', array('route' => 'feature_index'))
          ->setAttribute('icon', '&#xE865;');

      $menu->addChild('pricing', array('label' => 'menu.pricing.title'))
          ->setAttribute('dropdown', true)
          ->setAttribute('icon', '&#xE7EF;');
          $menu['pricing']->addChild('menu.pricing.pricing', array('route' => 'pricing_index'));
          $menu['pricing']->addChild('menu.pricing.pricing_feature', array('route' => 'pricingfeature_index'));

      $menu->addChild('menu.school_testimonial', array('route' => 'schooltestimonial_index'))
          ->setAttribute('icon', '&#xE85D;');
      $menu->addChild('menu.contact', array('route' => 'contact_index'))
          ->setAttribute('icon', '&#xE85D;');
      $menu->addChild('menu.aboutus', array('route' => 'aboutus_index'))
          ->setAttribute('icon', '&#xE85D;');

      $menu->addChild('users', array('label' => 'menu.users.title'))
          ->setAttribute('dropdown', true)
          ->setAttribute('icon', '&#xE7EF;');
          $menu['users']->addChild('menu.users.adminstrator', array('route' => 'administrator_index'));
          $menu['users']->addChild('menu.users.manager', array('route' => 'manager_index'));

      return $menu;
    }

    /**
     * set menu for the student
     *
     * @param array $options
     * @return FactoryInterface
     */
    public function studentParentMenu()
    {
      $menu = $this->factory->createItem('root');

      $menu->addChild('menu.dashbord', array('route' => 'dashbord_index'))
          ->setAttribute('icon', '&#xE871;');

      $menu->addChild('menu.administration.show_attendance', array('route' => 'attendance_parent_space'))
          ->setAttribute('icon', '&#xE0DF;');

      $menu->addChild('menu.schedule.show_schedule', array('route' => 'schedule_parent_space'))
          ->setAttribute('icon', '&#xE8B5;');

      $menu->addChild('menu.exam.show_exam',  array('route' => 'exam_date_parent_space'))
          ->setAttribute('icon', '&#xE14F;');

      $menu->addChild('menu.exam.show_note', array('route' => 'note_parent_space'))
          ->setAttribute('icon', '&#xE85D;');

      $menu->addChild('menu.administration.show_sanction', array('route' => 'sanction_parent_index'))
          ->setAttribute('icon', '&#xE0AF;');
      return $menu;
    }

    /**
     * set menu for the student
     *
     * @param array $options
     * @return FactoryInterface
     */
    public function professorMenu()
    {
      $menu = $this->factory->createItem('root');

      $menu->addChild('menu.dashbord', array('route' => 'dashbord_index'))
          ->setAttribute('icon', '&#xE871;');

      $menu->addChild('menu.administration.attendance', array('route' => 'attendance_student_new'))
          ->setAttribute('icon', '&#xE0DF;');

      $menu->addChild('menu.schedule.title', array('route' => 'schedule_professor_space'))
          ->setAttribute('icon', '&#xE8B5;');

      $menu->addChild('menu.exam.title', array('route' => 'exam_index'))
          ->setAttribute('icon', '&#xE85D;');

      $menu->addChild('menu.administration.title', array('route' => 'sanction_index'))
          ->setAttribute('icon', '&#xE0AF;');

      return $menu;
    }

    /**
     * @param array $options
     * @return FactoryInterface
     */
    public function createSidebarMenu(array $options)
    {
        $menu = $this->factory->createItem('sidebar')
                              ->setChildrenAttribute('class', 'nav menuzord-menu pull-right');
        $menu->addChild('menu.login.title', array('route' => 'login'));
        $menu->addChild('menu.contact.title', array('route' => 'contact_page'));
        return $menu;
    }

    /**
     * @param int $level     The level of the ancestor to be returned
     * @return \Knp\Menu\ItemInterface
     */
    public function getAncestor($level = null)
    {
        if ($level) {
            $obj = $this;

            do {
                if($obj->getLevel() == $level) {
                    return $obj;
                }
            } while ($obj = $obj->getParent());
        }

        return false;
    }
}
