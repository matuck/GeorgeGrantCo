<?php

namespace matuckGeorgeGrantCoBundle\Controller;

use Doctrine\ORM\NoResultException;
use matuckGeorgeGrantCoBundle\Entity\Page;
use matuckGeorgeGrantCoBundle\Entity\Setting;
use matuckGeorgeGrantCoBundle\Form\SettingType;
use matuckGeorgeGrantCoBundle\Form\Setting2Type;
use matuckGeorgeGrantCoBundle\Form\Setting3Type;
use matuckGeorgeGrantCoBundle\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use matuckGeorgeGrantCoBundle\Form\PageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        /** @var PageRepository $pageRepo */
        $pageRepo = $this->getDoctrine()
            ->getRepository('matuckGeorgeGrantCoBundle:Page');
        /** @var Page $page */
        $page = $pageRepo->findOneByHome(true);
        if(!$page)
        {
            throw new NotFoundHttpException("The page was not.  The home page is not set.");
        }
        return $this->render('matuckGeorgeGrantCoBundle:Default:page.html.twig', array('page' => $page));
    }

    /**
     * @param Request $request
     * @Route("/requestinfosent", name="requestinfosent")
     */
    public function requestInfoSent()
    {
        return $this->render('matuckGeorgeGrantCoBundle:Default:requestinfosent.html.twig');
    }

    /**
     * @param Request $request
     * @Route("/requestinfo", name="requestinfo")
     */
    public function requestInfo(Request $request)
    {
        $data = array();
        $form = $this->createFormBuilder($data);
        $form->add('Name', 'text', array('attr' => array('class' => 'form-control'), 'required' => false));
        $form->add('Title', 'text', array('attr' => array('class' => 'form-control'), 'required' => false));
        $form->add('Company','text', array('attr' => array('class' => 'form-control'), 'required' => false));
        $form->add('Address','text', array('attr' => array('class' => 'form-control'), 'required' => false));
        $form->add('CityStateZip','text', array('attr' => array('class' => 'form-control'), 'required' => false));
        $form->add('Telephone','text', array('attr' => array('class' => 'form-control')));
        $form->add('Fax','text', array('attr' => array('class' => 'form-control'), 'required' => false));
        $form->add('Email','text', array('attr' => array('class' => 'form-control')));
        $form->add('Comments', 'textarea', array('attr' => array('class' => 'form-control'), 'required' => false));
        $form->add('captchaCode', 'captcha', array(
            'captchaConfig' => 'captcha.config.basic_captcha'
        ));

        $captcha = $this->get('captcha')->setConfig('captcha.config.basic_captcha');
        $form = $form->getForm();

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $data = $form->getData();
                if($captcha->Validate($data['captchaCode']))
                {
                    // $data is a simply array with your form fields
                    // like "query" and "category" as defined above.
                    $messageContent = '';
                    if ($data['Name']) {
                        $messageContent = $messageContent . $data['Name'] . '<br />';
                    }
                    if ($data['Title']) {
                        $messageContent = $messageContent . $data['Title'] . '<br />';
                    }
                    if ($data['Company']) {
                        $messageContent = $messageContent . $data['Company'] . '<br />';
                    }
                    if ($data['Address']) {
                        $messageContent = $messageContent . $data['Address'] . '<br />';
                    }
                    if ($data['CityStateZip']) {
                        $messageContent = $messageContent . $data['CityStateZip'] . '<br />';
                    }
                    if ($data['Telephone']) {
                        $messageContent = $messageContent . $data['Telephone'] . '<br />';
                    }
                    if ($data['Fax']) {
                        $messageContent = $messageContent . $data['Fax'] . '<br />';
                    }
                    if ($data['Email']) {
                        $messageContent = $messageContent . $data['Email'] . '<br />';
                    }

                    $messageContent = $messageContent . "<br />";
                    $messageContent = $messageContent . "<br />";
                    if ($data['Comments']) {
                        $messageContent = $messageContent . $data['Comments'] . '<br />';
                    }
                    /** @var SettingRepository $settingRepo */
                    $settingRepo = $this->getDoctrine()
                        ->getRepository('matuckGeorgeGrantCoBundle:Setting');
                    $email = $settingRepo->findOneByName("siteEmail");
                    $message = \Swift_Message::newInstance();
                    $message->setSubject('Request from website')
                        ->setFrom($data['Email'])
                        ->setTo($email->getValue())
                        ->setBody($messageContent, 'text/html');
                    $this->get('mailer')->send($message);
                    return $this->redirectToRoute('requestinfosent');
                }
                else
                {
                    $form->get('captchaCode')->addError(new FormError('The captcha was invalid please try again.'));
                }
            }
        }

        return $this->render('matuckGeorgeGrantCoBundle:Default:requestinfo.html.twig', array('form' =>$form->createView()));
    }

    /**
     * @Route("/{slug}.html", name="page")
     * @param $slug
     */
    public function pageAction($slug)
    {
        /** @var PageRepository $pageRepo */
        $pageRepo = $this->getDoctrine()
            ->getRepository('matuckGeorgeGrantCoBundle:Page');
        /** @var Page $page */
        $page = $pageRepo->findOneBySlug($slug);
        if(!$page)
        {
            throw new NotFoundHttpException("The page was not.  The home page is not set.");
        }
        return $this->render('matuckGeorgeGrantCoBundle:Default:page.html.twig', array('page' => $page));
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction(Request $request)
    {
        /** @var SettingRepository $settingRepo */
        $settingRepo = $this->getDoctrine()
            ->getRepository('matuckGeorgeGrantCoBundle:Setting');
        $email = $settingRepo->findOneByName("siteEmail");
        if(!$email)
        {
            $email = new Setting();
            $email->setName("siteEmail");
        }

        $form = $this->createForm(new SettingType(), $email);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($email);
            $em->flush();
            return $this->redirectToRoute('admin');
        }

        $siteKeywords = $settingRepo->findOneByName("siteKeywords");
        if(!$siteKeywords)
        {
            $siteKeywords = new Setting();
            $siteKeywords->setName("siteKeywords");
        }
        $keywordsform = $this->createForm(new Setting3Type(), $siteKeywords);
        $keywordsform->handleRequest($request);
        if($keywordsform->isSubmitted() && $keywordsform->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($siteKeywords);
            $em->flush();
            return $this->redirectToRoute('admin');
        }

        $siteFooter = $settingRepo->findOneByName("siteFooter");
        if(!$siteFooter)
        {
            $siteFooter = new Setting();
            $siteFooter->setName("siteFooter");
        }
        $footerform = $this->createForm(new Setting2Type(), $siteFooter);

        $footerform->handleRequest($request);
        if($footerform->isSubmitted() && $footerform->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($siteFooter);
            $em->flush();
            return $this->redirectToRoute('admin');
        }
        return $this->render('matuckGeorgeGrantCoBundle:Default:admin.html.twig', array('form' => $form->createView(), 'footerform' => $footerform->createView(), 'keywordsform' => $keywordsform->createView()));
    }

    /**
     * @Route("/admin/pages", name="adminpages")
     */
    public function adminPagesAction()
    {
        /** @var PageRepository $pageRepo */
        $pageRepo = $this->getDoctrine()
            ->getRepository('matuckGeorgeGrantCoBundle:Page');
        $pages = $pageRepo->findBy([], ['title' => 'ASC']);
        return $this->render('matuckGeorgeGrantCoBundle:Default:adminpage.html.twig', array('pages' => $pages));
    }

    /**
     * @Route("/admin/pages/Create", name="adminpagesCreate")
     */
    public function adminPagesCreateAction(Request $request)
    {
        $page = new Page();
        $form = $this->createForm(new PageType(), $page);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            if($page->getHome()) {
                $pages = $em->getRepository('matuckGeorgeGrantCoBundle:Page')->findByHome(true);
                foreach ($pages as $currpage)
                {
                    $currpage->setHome(false);
                    $em->persist($currpage);
                }
            }
            $em->persist($page);
            $em->flush();
            return $this->redirectToRoute('adminpages');
        }

        return $this->render('matuckGeorgeGrantCoBundle:Default:adminpageCreate.html.twig', array('pageform' => $form->createView()));
    }

    /**
     * @Route("/admin/pages/Edit/{id}", requirements={"id" = "\d+"}, name="adminpagesEdit")
     */
    public function adminPagesEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $pagerepo = $em->getRepository('matuckGeorgeGrantCoBundle:Page');
        $page = $pagerepo->findOneById($id);
        $form = $this->createForm(new PageType(), $page);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            if($page->getHome()) {
                $pages = $em->getRepository('matuckGeorgeGrantCoBundle:Page')->findByHome(true);
                foreach ($pages as $currpage)
                {
                    $currpage->setHome(false);
                    $em->persist($currpage);
                }
                $page->setHome(true);
            }
            $em->persist($page);
            $em->flush();
            return $this->redirectToRoute('adminpages');
        }

        return $this->render('matuckGeorgeGrantCoBundle:Default:adminpageEdit.html.twig', array('pageform' => $form->createView()));
    }

    /**
     * @Route("/admin/pages/Delete/{id}", requirements={"id" = "\d+"},name="adminpagesDelete")
     */
    public function adminPagesDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $pagerepo = $em->getRepository('matuckGeorgeGrantCoBundle:Page');
        $page = $pagerepo->findOneById($id);
        $em->remove($page);
        $em->flush();
        return $this->redirectToRoute('adminpages');
    }

    /**
     * @Route("/admin/pages/SetHome/{id}", requirements={"id" = "\d+"},name="adminpagesSetHome")
     */
    public function adminPagesSetHomeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $pagerepo = $em->getRepository('matuckGeorgeGrantCoBundle:Page');
        $pages = $pagerepo->findByHome(true);
        $page = $pagerepo->findOneById($id);
        $page->setHome(true);
        foreach ($pages as $currpage)
        {
            $currpage->setHome(false);
            $em->persist($currpage);
        }
        $em->persist($page);
        $em->flush();
        return $this->redirectToRoute('adminpages');
    }

    /**
     * @Route("/admin/pages/previous/{id}", requirements={"id" = "\d+"},name="adminpagesPrevious")
     * @param $id the page id to see previous versions of.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminPagesPrevious($id)
    {
        $em = $this->getDoctrine()->getManager();
        $pagerepo = $em->getRepository('matuckGeorgeGrantCoBundle:Page');
        $page = $pagerepo->findOneById($id);
        $logrepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $previousVersions = $logrepo->getLogEntries($page);
        return $this->render('matuckGeorgeGrantCoBundle:Default:adminpagePrevious.html.twig', array('page' => $page, 'versions' => $previousVersions));
    }

    /**
     * @Route("/admin/pages/previous/view/{id}/{version}", requirements={"id" = "\d+", "version" = "\d+"},name="adminpagesPreviousView")
     * @param $id the page id to see previous versions of.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminPagesPreviousView($id, $version)
    {
        $em = $this->getDoctrine()->getManager();
        $pagerepo = $em->getRepository('matuckGeorgeGrantCoBundle:Page');
        $page = $pagerepo->findOneById($id);
        $logrepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $logrepo->revert($page, $version);
        return $this->render('matuckGeorgeGrantCoBundle:Default:adminpagesPreviousView.html.twig', array('page' => $page, 'version' => $version));
    }

    /**
     * @Route("/admin/pages/previous/revert/{id}/{version}", requirements={"id" = "\d+", "version" = "\d+"},name="adminpagesPreviousRevert")
     * @param $id the page id to see previous versions of.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminPagesPreviousRevert($id, $version)
    {
        $em = $this->getDoctrine()->getManager();
        $pagerepo = $em->getRepository('matuckGeorgeGrantCoBundle:Page');
        $page = $pagerepo->findOneById($id);
        $logrepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $logrepo->revert($page, $version);
        $em->persist($page);
        $em->flush();
        return $this->redirectToRoute('adminpages');
    }

    public function FooterAction()
    {
        /** @var SettingRepository $settingRepo */
        $settingRepo = $this->getDoctrine()
            ->getRepository('matuckGeorgeGrantCoBundle:Setting');
        $footer = $settingRepo->findOneByName("siteFooter");
        return $this->render('matuckGeorgeGrantCoBundle:Default:footer.html.twig', array('content' => $footer->getValue()));
    }

    public function KeywordsAction()
    {
        $response = new Response();
        /** @var SettingRepository $settingRepo */
        $settingRepo = $this->getDoctrine()
            ->getRepository('matuckGeorgeGrantCoBundle:Setting');
        $siteKeywords = $settingRepo->findOneByName("siteKeywords");
        if(!$siteKeywords)
        {
            $response->setContent('');
        }
        else
        {
            $response->setContent($siteKeywords->getValue());
        }
        return $response;
    }
}
