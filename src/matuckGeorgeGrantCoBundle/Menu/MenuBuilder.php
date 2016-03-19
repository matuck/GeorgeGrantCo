<?php

namespace matuckGeorgeGrantCoBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\AccessMap;
use matuckGeorgeGrantCoBundle\Entity\MenuEntry;
use matuckGeorgeGrantCoBundle\Repository\MenuEntryRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class MenuBuilder
 * @package matuckGeorgeGrantCoBundle\Menu
 *
 * To add a new menu to the system add a new tag line to services.yml and create a method below to create it
 *- { name: knp_menu.menu_builder, method: createMainMenu, alias: main }
 * To call the menu in the template put
 * {{ knp_menu_render('main') }}  where main is the the alias you created in the tag
 */
class MenuBuilder
{
    private $factory;

    private $authCheck;

    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorization, AccessMap $accessmap, EntityManager $em)
    {
        $this->factory = $factory;
        $this->authCheck = $authorization;
        $this->am = $accessmap;
        $this->em = $em;
        /* @var MenuEntryRepository $this->MenuRepo */
        $this->MenuRepo = $em->getRepository('matuckGeorgeGrantCoBundle:MenuEntry');
    }

    public function createMainMenu(array $options)
    {
        $mainmenu = $this->MenuRepo->findOneByTitle("Main Menu");
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav nav-justified');
        $this->buildMenu($menu, $mainmenu);
        return $menu;
    }

    public function adminMenu($menu = null)
    {
        if (!$this->authCheck->isGranted('ROLE_ADMIN')) {
            return;
        }
        if($menu == null)
        {
            $adminMenu = $this->factory->createItem('root');
        } else {
            $adminMenu = $menu->addChild('Admin', array('route' => 'admin'));
        }
        $adminMenu->addChild('Manage Pages', array('route' => 'adminpages'));
        $adminMenu->addChild('Manage Product Categories', array('route' => 'adminproductcategory'));
        $adminMenu->addChild('Manage Product', array('route' => 'adminproduct'));
        return $adminMenu;
    }

    private function buildMenu(&$retval, $menu)
    {
        /* @var MenuEntry $entry */
        foreach($menu->getChildren() as $entry)
        {
            $roles = $this->getRoles($entry->getUrl());
            $process = false;
            if(count($roles) > 0)
            {
                foreach($roles as $role)
                {
                    if($this->authCheck->isGranted($role))
                    {
                        $process = true;
                        break;
                    }
                }
            }
            else
            {
                $process = true;
            }
            if($process)
            {
                $attributes = array();
                if(count($entry->getChildren()) > 0)
                {
                    $attributes['class'] = "dropdown";
                }
                if(count($entry->getChildren()) > 0)
                {
                    $thismenu = $retval->addChild($entry->getTitle() . '<span class="caret"></span>', array('uri' => $entry->getUrl(), 'attributes' => $attributes));
                    $thismenu->setChildrenAttribute('class', 'dropdown-menu');
                    /* @var MenuItem $thismenu */
                    $thismenu->setLinkAttributes(array('data-toggle' => 'dropdown'));
                    $this->buildMenu($thismenu, $entry);
                }
                else
                {
                    $thismenu = $retval->addChild($entry->getTitle(), array('uri' => $entry->getUrl(), 'attributes' => $attributes));
                }
            }
        }
    }

    private function getRoles($path){ //$path is the path you want to check access to

        //build a request based on path to check access
        $request = Request::create($path,'GET');
        list($roles,$channel) = $this->am->getPatterns($request);//get access_control for this request
    return $roles;
}
}
