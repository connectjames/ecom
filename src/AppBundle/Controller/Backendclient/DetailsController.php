<?php

namespace AppBundle\Controller\Backendclient;

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\UserEditAddressDeliveryForm;
use AppBundle\Form\UserEditAddressInvoiceForm;
use AppBundle\Form\UserForgotPassword;
use AppBundle\Service\BasketService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/my-account")
 * @Security("is_granted('ROLE_USER')")
 */

class DetailsController extends Controller
{
    /**
     * @Route("", name="my-account_details")
     */
    public function detailsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $formEditAddressInvoice = $this->createForm(UserEditAddressInvoiceForm::class);

        $formEditAddressInvoice->handleRequest($request);

        $formEditAddressDelivery = $this->createForm(UserEditAddressDeliveryForm::class);

        $formEditAddressDelivery->handleRequest($request);

        $formUserForgotPassword = $this->createForm(UserForgotPassword::class);

        $formUserForgotPassword->handleRequest($request);

        if ($formEditAddressInvoice->isValid()) {

            // Invoice address creation
            $invoiceAddress = array("firstName" => $formEditAddressInvoice['firstName']->getData(), "lastName" => $formEditAddressInvoice['lastName']->getData(), "company" => $formEditAddressInvoice['company']->getData(), "address1" => $formEditAddressInvoice['address1']->getData(), "address2" => $formEditAddressInvoice['address2']->getData(), "city" => $formEditAddressInvoice['city']->getData(), "postcode" => $formEditAddressInvoice['postcode']->getData(), "phone" => $formEditAddressInvoice['phone']->getData());

            /** @var User $user */
            $user = $this->getUser();

            $user->setFirstName($formEditAddressInvoice['firstName']->getData());
            $user->setLastName($formEditAddressInvoice['lastName']->getData());
            $user->setInvoiceAddress(json_encode($invoiceAddress));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Your invoice details have been updated.');
        }

        if ($formUserForgotPassword->isValid()) {

            $userPassword = $formUserForgotPassword->getData();

            $userPassword = $userPassword["plainPassword"];

            $user = $this->getUser();

            $user->setPlainPassword($userPassword);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Your Password has successfully been updated.');

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

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
        $featuredProducts = $em->getRepository('AppBundle:Product')
            ->findBy(array(
                'featured' => 1,
            ));

        // Makes the products, products qty and the basket total available to the view if a basket exists
        $session = $request->getSession();

        $basketProducts = 0;
        $basketQty = 0;
        $basketTotal = 0;

        // Check if a basket exists and if yes makes the products, products qty and the basket total available to the view
        if ($session->get('basketProducts')) {
            $basketProducts = $session->get('basketProducts');
            $basketQty = $session->get('basketQty');
            $basketTotal = $session->get('basketTotal');
        }

        // Make the user details available to the view

        /** @var User $user */
        $user = $this->getUser();

        return $this->render('backendclient/details.html.twig', array(
            'user' => $user,
            'formUserForgotPassword' => $formUserForgotPassword->createView(),
            'formEditAddressDelivery' => $formEditAddressDelivery->createView(),
            'formEditAddressInvoice' => $formEditAddressInvoice->createView(),
            'featuredProducts' => $featuredProducts,
            'basketProducts' => $basketProducts,
            'basketQty' => $basketQty,
            'basketTotal' => $basketTotal,
        ));
    }

    /**
     * @Route("/details/invoice/{id}", name="my-account_invoice")
     */
    public function invoiceAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository('AppBundle:Order')
            ->findOneBy(array(
                'id' => $id
            ));

        // Creates a PDF file with the orders in (https://github.com/KnpLabs/snappy)
        $snappy = $this->get('knp_snappy.pdf');
        $html = $this->renderView('backendclient/printInvoice.html.twig', array(
            'order' => $order
        ));
        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="'. $id . '-' . $this->getParameter('company_name_url_friendly') . '-invoices.pdf"'
            )
        );
    }

    /**
     * @Route("/change-address/saved-address/{id}", name="select_different_address_saved")
     */
    public function selectDifferentAddressSavedAction($id)
    {
        return $this->render('backendclient/delivery/_savedAddress.html.twig', array(
            'id' => $id
        ));
    }

    /**
     * @Route("/save/saved-address", name="save_address_saved")
     */
    public function saveAddressSavedAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $deliveryAddresses = $user->getDeliveryAddress();

        $id = $request->query->get('id');

        $deliveryAddresses[$id] = json_decode($request->query->get('deliveryAddress'), true);

        $user->setDeliveryAddress($deliveryAddresses);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('my-account_details');
    }

    /**
     * @Route("/delete/saved-address/{id}", name="delete_address_saved")
     */
    public function deleteAddressSavedAction($id)
    {
        /** @var User $user */
        $user = $this->getUser();

        $deliveryAddresses = $user->getDeliveryAddress();

        unset($deliveryAddresses[$id]);

        $deliveryAddresses = array_values($deliveryAddresses);

        $user->setDeliveryAddress($deliveryAddresses);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'You have successfully deleted this delivery address.');
        return $this->redirectToRoute('my-account_details');
    }
}


