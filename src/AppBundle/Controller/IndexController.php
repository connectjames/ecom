<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ContactUsForm;
use AppBundle\Service\BasketService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class IndexController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->query->get('id')) {

            $id = $request->query->get('id');

            /** @var Product $product */
            $product = $em->getRepository('AppBundle:Product')
                ->findOneBy(array(
                    'id' => $id
                ));

            // If product exists
            if (!$product) {
                throw $this->createNotFoundException('Product not found');
            }

            $quantity = 1;

            $basketService = new BasketService();

            $session = $request->getSession();

            if (count($session->get('basketProducts'))) {

                // If a basket does already exist then...
                $basketService->addToBasket($product, $quantity, $request);

            } else {

                // If a basket does not already exist then...
                $basketService->createBasket($product, $quantity, $request);
            }

            $this->addFlash('success', 'Product added to basket');

            return $this->redirectToRoute('basket');
        }

        // Find the featured products and their details

        /** @var Product $featuredProducts */
        $featuredProducts = $em->getRepository('AppBundle:Product')->findBy(
            array('featured' => 1)
        );

        // Makes the products, products qty and the basket total available to the view if a basket exists
        $session = $request->getSession();

        $basketProducts = $session->get('basketProducts');
        $basketQty = $session->get('basketQty');
        $basketTotal = $session->get('basketTotal');

        return $this->render('index/index.html.twig', array(
            'featuredProducts' => $featuredProducts,
            'basketProducts' => $basketProducts,
            'basketQty' => $basketQty,
            'basketTotal' => $basketTotal
        ));
    }

    /**
     * @Route("/about-us", name="about_us")
     */
    public function aboutUsAction(Request $request)
    {
        // Makes the products, products qty and the basket total available to the view if a basket exists
        $session = $request->getSession();

        $basketProducts = $session->get('basketProducts');
        $basketQty = $session->get('basketQty');
        $basketTotal = $session->get('basketTotal');

        return $this->render('index/about-us.html.twig', array(
            'basketProducts' => $basketProducts,
            'basketQty' => $basketQty,
            'basketTotal' => $basketTotal
        ));
    }

    /**
     * @Route("/search", name="search")
     */
    public function searchAction(Request $request)
    {
        // Makes the products, products qty and the basket total available to the view if a basket exists
        $session = $request->getSession();

        $basketProducts = $session->get('basketProducts');
        $basketQty = $session->get('basketQty');
        $basketTotal = $session->get('basketTotal');

        return $this->render('index/search.html.twig', array(
            'basketProducts' => $basketProducts,
            'basketQty' => $basketQty,
            'basketTotal' => $basketTotal
        ));
    }

    /**
     * @Route("/contact-us", name="contact_us")
     */
    public function contactUsAction(Request $request)
    {
        $formContactUs = $this->createForm(ContactUsForm::class);

        $formContactUs->handleRequest($request);

        if ($formContactUs->isSubmitted() && $formContactUs->isValid()) {

            $contactFormData = $formContactUs->getData();

            $message = \Swift_Message::newInstance()
                ->setSubject('Contact Us -' . $this->getParameter('company_name') . ' - SKD Web Enquiry')
                ->setFrom($this->getParameter('company_email'), $this->getParameter('company_name'))
                ->setTo($this->getParameter('company_email'))
                ->setBody(
                    $this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'email/newContactForm.html.twig',
                        array(
                            'contactFormData' => $contactFormData
                        )

                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            $message = \Swift_Message::newInstance()
                ->setSubject('Contact Us - ' . $this->getParameter('company_name') . ' - SKD Web Enquiry')
                ->setFrom($this->getParameter('company_email'), $this->getParameter('company_name'))
                ->setTo($this->getParameter('admin_email'))
                ->setBody(
                    $this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'email/newContactForm.html.twig',
                        array(
                            'contactFormData' => $contactFormData
                        )

                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            $this->addFlash('success', 'Thank you for your enquiry ' . $formContactUs['fullName']->getData());
        }

        // Makes the products, products qty and the basket total available to the view if a basket exists
        $session = $request->getSession();

        $basketProducts = $session->get('basketProducts');
        $basketQty = $session->get('basketQty');
        $basketTotal = $session->get('basketTotal');

        return $this->render('index/contact-us.html.twig', array(
            'formContactUs' => $formContactUs->createView(),
            'basketProducts' => $basketProducts,
            'basketQty' => $basketQty,
            'basketTotal' => $basketTotal
        ));
    }

    /**
     * @Route("/terms", name="terms_and_conditions")
     */
    public function termsAction(Request $request)
    {
        // Makes the products, products qty and the basket total available to the view if a basket exists
        $session = $request->getSession();

        $basketProducts = $session->get('basketProducts');
        $basketQty = $session->get('basketQty');
        $basketTotal = $session->get('basketTotal');

        return $this->render('index/terms.html.twig', array(
            'basketProducts' => $basketProducts,
            'basketQty' => $basketQty,
            'basketTotal' => $basketTotal
        ));
    }

    /**
     * @Route("/privacy", name="privacy")
     */
    public function privacyAction(Request $request)
    {
        // Makes the products, products qty and the basket total available to the view if a basket exists
        $session = $request->getSession();

        $basketProducts = $session->get('basketProducts');
        $basketQty = $session->get('basketQty');
        $basketTotal = $session->get('basketTotal');

        return $this->render('index/privacy.html.twig', array(
            'basketProducts' => $basketProducts,
            'basketQty' => $basketQty,
            'basketTotal' => $basketTotal
        ));
    }
}


