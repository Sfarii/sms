<?php
namespace API\Menu;

use Knp\Menu\FactoryInterface;

class MenuBuilder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array $options
     * @return FactoryInterface
     */
    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('menu.dashbord', array('route' => 'attendanceprofessor_index'))
            ->setAttribute('icon', '&#xE871;');

        $menu->addChild('establishment', array('label' => 'menu.establishment.title'))
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', '&#xE80C;');
            $menu['establishment']->addChild('menu.establishment.division', array('route' => 'division_index'));
            $menu['establishment']->addChild('menu.establishment.grade', array('route' => 'grade_index'));
            $menu['establishment']->addChild('menu.establishment.section', array('route' => 'section_index'));

        $menu->addChild('users', array('label' => 'menu.users.title'))
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', '&#xE7EF;');
            $menu['users']->addChild('menu.users.student', array('route' => 'student_index'));
            $menu['users']->addChild('menu.users.parentstudent', array('route' => 'studentparent_index'));
            $menu['users']->addChild('menu.users.professor', array('route' => 'professor_index'));
            $menu['users']->addChild('menu.users.adminstrator', array('route' => 'administrator_index'));
        
        $menu->addChild('menu.course', array('route' => 'course_index'))
            ->setAttribute('icon', '&#xE865;');

        $menu->addChild('menu.config_schedule.session', array('route' => 'session_index'))
            ->setAttribute('icon', '&#xE425;');

        $menu->addChild('attendance', array('label' => 'menu.administration.attendance'))
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', '&#xE0DF;');   
            $menu['attendance']->addChild('menu.administration.attendance_student', array('route' => 'attendance_student_new'));
            $menu['attendance']->addChild('menu.administration.attendance_professor', array('route' => 'attendance_professor_new'));


        $menu->addChild('menu.schedule.title', array('route' => 'schedule_index'))
            ->setAttribute('icon', '&#xE8B5;');
        
        $menu->addChild('exam', array('label' => 'menu.exam.title'))
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', '&#xE14F;');       
            $menu['exam']->addChild('menu.exam.examType', array('route' => 'typeexam_index'));
            $menu['exam']->addChild('menu.exam.exam', array('route' => 'exam_index'));

        $menu->addChild('menu.exam.note', array('route' => 'note_index'))
            ->setAttribute('icon', '&#xE85D;');   
        
        $menu->addChild('administration', array('label' => 'menu.administration.title'))
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', '&#xE0AF;');       
            $menu['administration']->addChild('menu.administration.sanction', array('route' => 'sanction_index')); 
        

        return $menu;
    }

    /**
     * @param array $options
     * @return FactoryInterface
     */
    public function createSidebarMenu(array $options)
    {
        $menu = $this->factory->createItem('sidebar');

        if (isset($options['include_homepage']) && $options['include_homepage']) {
            $menu->addChild('Home', array('route' => 'homepage'));
        }

        // ... add more children

        return $menu;
    }
}