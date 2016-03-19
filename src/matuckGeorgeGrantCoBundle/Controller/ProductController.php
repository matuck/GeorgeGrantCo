<?php

namespace matuckGeorgeGrantCoBundle\Controller;

use matuckGeorgeGrantCoBundle\Entity\Setting;
use matuckGeorgeGrantCoBundle\Form\SettingType;
use matuckGeorgeGrantCoBundle\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use matuckGeorgeGrantCoBundle\Entity\Product;
use matuckGeorgeGrantCoBundle\Form\ProductType;

class ProductController extends Controller
{
    /**
     * @Route("/admin/product", name="adminproduct")
     */
    public function adminProduct()
    {
        /** @var ProductRepository $productRepo */
        $productRepo = $this->getDoctrine()
            ->getRepository('matuckGeorgeGrantCoBundle:Product');
        $products = $productRepo->findBy([], ['name' => 'ASC']);
        return $this->render('matuckGeorgeGrantCoBundle:Product:adminproduct.html.twig', array('products' => $products));
    }

    /**
     * @Route("/admin/product/Create", name="adminproductCreate")
     */
    public function adminProductCreateAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(new ProductType(), $product);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('adminproduct');
        }

        return $this->render('matuckGeorgeGrantCoBundle:Product:adminproductCreate.html.twig', array('productform' => $form->createView()));
    }

    /**
     * @Route("/admin/product/edit/{id}", requirements={"id" = "\d+"},name="adminproductEdit")
     */
    public function adminProductEdit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $productrepo = $em->getRepository('matuckGeorgeGrantCoBundle:Product');
        $product = $productrepo->findOneById($id);
        $form = $this->createForm(new ProductType(), $product);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();


            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('adminproduct');
        }

        return $this->render('matuckGeorgeGrantCoBundle:Product:adminproductEdit.html.twig', array('productform' => $form->createView()));
    }

    /**
     * @Route("/admin/product/delete/{id}", requirements={"id" = "\d+"}, name="adminproductDelete")
     */
    public function adminProductDelete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $productrepo = $em->getRepository('matuckGeorgeGrantCoBundle:Product');
        $product = $productrepo->findOneById($id);
        $em->remove($product);
        $em->flush();
        return $this->redirectToRoute('adminproduct');
    }

    /**
     * @Route("/admin/product/previous/{id}", requirements={"id" = "\d+"},name="adminproductPrevious")
     */
    public function adminProductPrevious($id)
    {
        $em = $this->getDoctrine()->getManager();
        $productrepo = $em->getRepository('matuckGeorgeGrantCoBundle:Product');
        $product = $productrepo->findOneById($id);
        $logrepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $previousVersions = $logrepo->getLogEntries($product);
        return $this->render('matuckGeorgeGrantCoBundle:Product:adminproductPrevious.html.twig', array('product' => $product, 'versions' => $previousVersions));
    }

    /**
     * @Route("/admin/product/previous/view/{id}/{version}", requirements={"id" = "\d+", "version" = "\d+"},name="adminproductPreviousView")
     * @param $id the category id to see previous versions of.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminProductPreviousView($id, $version)
    {
        $em = $this->getDoctrine()->getManager();
        $productrepo = $em->getRepository('matuckGeorgeGrantCoBundle:Product');
        $product = $productrepo->findOneById($id);
        $logrepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $logrepo->revert($product, $version);
        return $this->render('matuckGeorgeGrantCoBundle:Product:adminproductPreviousView.html.twig', array('product' => $product, 'version' => $version));
    }

    /**
     * @Route("/admin/product/previous/revert/{id}/{version}", requirements={"id" = "\d+", "version" = "\d+"},name="adminproductPreviousRevert")
     * @param $id the page id to see previous versions of.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminproductPreviousRevert($id, $version)
    {
        $em = $this->getDoctrine()->getManager();
        $productrepo = $em->getRepository('matuckGeorgeGrantCoBundle:Product');
        $product = $productrepo->findOneById($id);
        $logrepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $logrepo->revert($product, $version);
        $em->persist($product);
        $em->flush();
        return $this->redirectToRoute('adminproduct');
    }
}
