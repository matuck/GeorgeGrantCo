<?php

namespace matuckGeorgeGrantCoBundle\Controller;

use matuckGeorgeGrantCoBundle\Entity\MenuEntry;
use matuckGeorgeGrantCoBundle\Form\MenuEntryType;
use matuckGeorgeGrantCoBundle\Repository\MenuEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends Controller
{
    /**
     * @Route("/admin/menu", name="adminmenu")
     * @param $slug
     */
    public function adminMenu()
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckGeorgeGrantCoBundle:MenuEntry');
        /* @var MenuEntry $topmenu */
        $topmenu = $repo->findOneByTitle("Main Menu");

        $menu = $repo->childrenHierarchy($topmenu, false);
        return $this->render('matuckGeorgeGrantCoBundle:Menu:index.html.twig', array('menu' => $topmenu));
    }

    /**
     * @Route("/admin/menu/add/{id}", name="adminmenuadd")
     * @param $id
     */
    public function adminMenuAdd(Request $request, $id = null)
    {
        $parent = null;
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckGeorgeGrantCoBundle:MenuEntry');

        if($id != -1)
        {
            $parent = $repo->findOneById($id);
        }
        $menuEntry = new MenuEntry();
        $form = $this->createForm(new MenuEntryType(), $menuEntry);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            if($parent)
            {
                $menuEntry->setParent($parent);
            }
            $em->persist($menuEntry);
            $em->flush();
            return $this->redirectToRoute('adminmenu');
        }
        return $this->render('matuckGeorgeGrantCoBundle:Menu:createMenu.html.twig', array('parentmenu' => $parent, 'menuForm' => $form->createView()));

    }

    /**
     * @Route("/admin/menu/edit/{id}", name="adminmenuedit")
     * @param $id
     */
    public function adminMenuEdit(Request $request, $id)
    {
        $repo = $this->getDoctrine()->getRepository('matuckGeorgeGrantCoBundle:MenuEntry');
        $entry = $repo->findOneById($id);
        $form = $this->createForm(new MenuEntryType(), $entry);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entry);
            $em->flush();
            return $this->redirectToRoute('adminmenu');
        }
        return $this->render('matuckGeorgeGrantCoBundle:Menu:editMenu.html.twig', array('menu' => $entry, 'menuForm' => $form->createView()));
    }
    /**
     * @Route("/admin/menu/delete/{id}", name="adminmenudelete")
     * @param $id of menu to delete
     */
    public function adminMenuDelete($id)
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckGeorgeGrantCoBundle:MenuEntry');
        $menu = $repo->findOneById($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($menu);
        $repo->recover();
        $em->flush();
        return $this->redirectToRoute('adminmenu');
    }

    /**
     * * @Route("/admin/menu/up/{id}", name="adminmenuup")
     * @param $id of the menu
     */
    public function adminMenuUp($id)
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckGeorgeGrantCoBundle:MenuEntry');
        $entry = $repo->findOneById($id);
        $repo->moveUp($entry);
        return $this->redirectToRoute('adminmenu');
    }

    /**
     * @Route("/admin/menu/down/{id}", name="adminmenudown")
     * @param $id of the menu
     */
    public function adminMenuDown($id)
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckGeorgeGrantCoBundle:MenuEntry');
        $entry = $repo->findOneById($id);
        $repo->moveDown($entry);
        return $this->redirectToRoute('adminmenu');
    }
}
