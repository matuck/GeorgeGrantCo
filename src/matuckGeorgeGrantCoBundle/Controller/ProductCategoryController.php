<?php

namespace matuckGeorgeGrantCoBundle\Controller;

use matuckGeorgeGrantCoBundle\Entity\ProductCategory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use matuckGeorgeGrantCoBundle\Form\ProductCategoryType;

class ProductCategoryController extends Controller
{

    /**
     * @Route("/products/{slug}.html", name="productcategoryView")
     */
    public function productcategoryView($slug)
    {
        /** @var ProductCategoryRepository $categoryRepo */
        $categoryRepo = $this->getDoctrine()
            ->getRepository('matuckGeorgeGrantCoBundle:ProductCategory');
        $category = $categoryRepo->findOneBySlug($slug);
        return $this->render('matuckGeorgeGrantCoBundle:ProductCategory:category.html.twig', array('category' => $category));
    }

    /**
     * @Route("/admin/productcategory", name="adminproductcategory")
     */
    public function adminProdCategory()
    {
        /** @var ProductCategoryRepository $categoryRepo */
        $categoryRepo = $this->getDoctrine()
            ->getRepository('matuckGeorgeGrantCoBundle:ProductCategory');
        $categories = $categoryRepo->findBy([], ['name' => 'ASC']);
        return $this->render('matuckGeorgeGrantCoBundle:ProductCategory:adminproduct.html.twig', array('categories' => $categories));
    }

    /**
     * @Route("/admin/productcategory/Create", name="adminproductcategoryCreate")
     */
    public function adminProductCategoryCreateAction(Request $request)
    {
        $category = new ProductCategory();
        $form = $this->createForm(new ProductCategoryType(), $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('adminproductcategory');
        }

        return $this->render('matuckGeorgeGrantCoBundle:ProductCategory:adminproductcategoryCreate.html.twig', array('categoryform' => $form->createView()));
    }

    /**
     * @Route("/admin/productcategory/edit/{id}", requirements={"id" = "\d+"},name="adminproductcategoryEdit")
     */
    public function adminProdCategoryEdit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $categoryrepo = $em->getRepository('matuckGeorgeGrantCoBundle:ProductCategory');
        $category = $categoryrepo->findOneById($id);
        $form = $this->createForm(new ProductCategoryType(), $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();


            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('adminproductcategory');
        }

        return $this->render('matuckGeorgeGrantCoBundle:ProductCategory:adminproductEdit.html.twig', array('categoryform' => $form->createView()));
    }

    /**
     * @Route("/admin/productcategory/delete/{id}", requirements={"id" = "\d+"}, name="adminproductcategoryDelete")
     */
    public function adminProdCategoryDelete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryrepo = $em->getRepository('matuckGeorgeGrantCoBundle:ProductCategory');
        $category = $categoryrepo->findOneById($id);
        $em->remove($category);
        $em->flush();
        return $this->redirectToRoute('adminproductcategory');
    }

    /**
     * @Route("/admin/productcategory/previous/{id}", requirements={"id" = "\d+"},name="adminproductcategoryPrevious")
     */
    public function adminProdCategoryPrevious($id)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryrepo = $em->getRepository('matuckGeorgeGrantCoBundle:ProductCategory');
        $category = $categoryrepo->findOneById($id);
        $logrepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $previousVersions = $logrepo->getLogEntries($category);
        return $this->render('matuckGeorgeGrantCoBundle:ProductCategory:adminproductPrevious.html.twig', array('category' => $category, 'versions' => $previousVersions));
    }

    /**
     * @Route("/admin/productcategory/previous/view/{id}/{version}", requirements={"id" = "\d+", "version" = "\d+"},name="adminproductcategoryPreviousView")
     * @param $id the category id to see previous versions of.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminProdCategoryPreviousView($id, $version)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryrepo = $em->getRepository('matuckGeorgeGrantCoBundle:ProductCategory');
        $category = $categoryrepo->findOneById($id);
        $logrepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $logrepo->revert($category, $version);
        return $this->render('matuckGeorgeGrantCoBundle:ProductCategory:adminproductPreviousView.html.twig', array('category' => $category, 'version' => $version));
    }

    /**
     * @Route("/admin/productcategory/previous/revert/{id}/{version}", requirements={"id" = "\d+", "version" = "\d+"},name="adminproductcategoryPreviousRevert")
     * @param $id the page id to see previous versions of.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminproductcategoryPreviousRevert($id, $version)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryrepo = $em->getRepository('matuckGeorgeGrantCoBundle:ProductCategory');
        $category = $categoryrepo->findOneById($id);
        $logrepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $logrepo->revert($category, $version);
        $em->persist($category);
        $em->flush();
        return $this->redirectToRoute('adminproductcategory');
    }

    /**
     * @Route("/admin/productcategory/productsort/{id}", requirements={"id" = "\d+", "version" = "\d+"},name="adminproductsort")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminProductSort(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryrepo = $em->getRepository('matuckGeorgeGrantCoBundle:ProductCategory');
        $category = $categoryrepo->findOneById($id);
        if($request->isMethod('POST'))
        {
            $order = $request->get('order');
            /* @var \matuckGeorgeGrantCoBundle\Entity\Product $product */
            foreach($category->getProducts() as $product)
            {
                $key = array_search($product->getId(), $order);
                if($key === false)
                {
                    $product->setOrder(999999);
                    $em->persist($product);
                }
                else
                {
                    if($key+1 != $product->getOrder())
                    {
                        $product->setOrder($key+1);
                        $em->persist($product);
                    }
                }
            }
            $em->flush();
            return $this->redirectToRoute('adminproductsort', ['id' => $id]);
        }

        return $this->render('matuckGeorgeGrantCoBundle:ProductCategory:adminproductSort.html.twig', array('category' => $category));
    }
}
