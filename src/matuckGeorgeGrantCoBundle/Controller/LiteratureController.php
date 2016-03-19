<?php

namespace matuckGeorgeGrantCoBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use matuckGeorgeGrantCoBundle\Entity\Literature;
use matuckGeorgeGrantCoBundle\Form\LiteratureType;
use matuckGeorgeGrantCoBundle\Repository\LiteratureRepository;

class LiteratureController extends Controller
{
    /**
     * @Route("/literature.htm", name="literature")
     */
    public function IndexAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getEntityManager();
        /** @var LiteratureRepository $litRepo */
        $litRepo = $em->getRepository('matuckGeorgeGrantCoBundle:Literature');
        $literatures = $litRepo->findBy(array(), array('order' => 'ASC'));

        return $this->render('matuckGeorgeGrantCoBundle:Literature:index.html.twig', array('literatures' => $literatures));
    }

    /**
     * @Route("/admin/literature", name="adminliterature")
     * @param Request $request
     */
    public function adminLiteratureAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getEntityManager();
        /** @var LiteratureRepository $litRepo */
        $litRepo = $em->getRepository('matuckGeorgeGrantCoBundle:Literature');
        $literatures = $litRepo->findBy(array(), array('order' => 'ASC'));
        if($request->isMethod('POST')) {
            $order = $request->get('order');
            /* @var Literature $literature */
            foreach ($literatures as $literature) {
                $key = array_search($literature->getId(), $order);
                if ($key === false) {
                    $literature->setOrder(999999);
                    $em->persist($literature);
                } else {
                    if ($key + 1 != $literature->getOrder()) {
                        $literature->setOrder($key + 1);
                        $em->persist($literature);
                    }
                }
            }
            $em->flush();
            return $this->redirectToRoute('adminliterature');
        }
        return $this->render('matuckGeorgeGrantCoBundle:Literature:admin.html.twig', array('literatures' => $literatures));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/literature/add", name="adminliteratureAdd")
     */
    public function adminLiteratureAddAction(Request $request)
    {
        $literature = new Literature();
        $form = $this->createForm(new LiteratureType(), $literature);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($literature);
            $em->flush();
            return $this->redirectToRoute('adminliterature');
        }
        return $this->render('matuckGeorgeGrantCoBundle:Literature:adminCreate.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/literature/Edit/{id}", requirements={"id" = "\d+"}, name="adminliteratureEdit")
     */
    public function adminLiteratureEditAction(Request $request, $id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getEntityManager();
        /** @var LiteratureRepository $litRepo */
        $litRepo = $em->getRepository('matuckGeorgeGrantCoBundle:Literature');
        $literature = $litRepo->findOneById($id);
        $form = $this->createForm(new LiteratureType(), $literature);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($literature);
            $em->flush();
            return $this->redirectToRoute('adminliterature');
        }
        return $this->render('matuckGeorgeGrantCoBundle:Literature:adminEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param $id
     * @Route("/admin/literature/Delete/{id}", requirements={"id" = "\d+"}, name="adminliteratureDelete")
     */
    public function adminLiteratureDeleteAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getEntityManager();
        /** @var LiteratureRepository $litRepo */
        $litRepo = $em->getRepository('matuckGeorgeGrantCoBundle:Literature');
        $literature = $litRepo->findOneById($id);
        $em->remove($literature);
        $em->flush();
        return $this->redirectToRoute('adminliterature');
    }
}
