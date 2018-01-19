<?php

namespace AppBundle\Controller;

use AppBundle\Entity\County;
use AppBundle\Entity\Dropshipper;
use AppBundle\Entity\Order;
use AppBundle\Entity\Product;
use AppBundle\Entity\Status;
use AppBundle\Entity\User;
use Knp\Snappy\Pdf;
use AppBundle\Form\CheckoutDeliveryForm;
use AppBundle\Form\CheckoutInvoiceForm;
use AppBundle\Form\OrderForm;
use AppBundle\Service\Delivery;
use AppBundle\Service\Basket;
use AppBundle\Service\DeliveryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CheckoutController extends Controller
{
    /**
     * @Route("/checkout-invoice", name="checkout_invoice")
     */
    public function checkoutInvoiceAction(Request $request)
    {
        $session = $request->getSession();

        $formCheckoutInvoiceForm = $this->createForm(CheckoutInvoiceForm::class);

        $formCheckoutInvoiceForm->handleRequest($request);

        if ($formCheckoutInvoiceForm->isSubmitted() && $formCheckoutInvoiceForm->isValid()) {

            // Invoice address creation
            $invoiceDetails['invoiceAddress'] = array("firstName" => $formCheckoutInvoiceForm['firstName']->getData(), "lastName" => $formCheckoutInvoiceForm['lastName']->getData(), "company" => $formCheckoutInvoiceForm['company']->getData(), "address1" => $formCheckoutInvoiceForm['address1']->getData(), "address2" => $formCheckoutInvoiceForm['address2']->getData(), "city" => $formCheckoutInvoiceForm['city']->getData(), "postcode" => $formCheckoutInvoiceForm['postcode']->getData(), "phone" => $formCheckoutInvoiceForm['phone']->getData());

            $invoiceDetails['email'] = $formCheckoutInvoiceForm['email']->getData();
            $invoiceDetails['firstName'] = $formCheckoutInvoiceForm['firstName']->getData();
            $invoiceDetails['lastName'] = $formCheckoutInvoiceForm['lastName']->getData();
            $invoiceDetails['purchaseOrderNumber'] = $formCheckoutInvoiceForm['purchaseOrderNumber']->getData();

            $session->set('invoiceDetails', $invoiceDetails);

            return $this->redirectToRoute('checkout_delivery');

        }

        // Makes the products, products qty and the basket total available to the view if a basket exists

        $basketProducts = $session->get('basketProducts');

        if (!$basketProducts) {
            return $this->redirectToRoute('basket');
        }

        $basketDetails = $session->get('basketDetails');
        $invoiceDetails = $session->get('invoiceDetails');

        return $this->render('frontend/checkout-invoice.html.twig', array(
            'formCheckoutInvoiceForm' => $formCheckoutInvoiceForm->createView(),
            'basketDetails' => $basketDetails,
            'invoiceDetails' => $invoiceDetails
        ));
    }

    /**
     * @Route("/checkout-delivery", name="checkout_delivery")
     */
    public function checkoutDeliveryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();

        $formCheckoutDeliveryForm = $this->createForm(CheckoutDeliveryForm::class);

        $formCheckoutDeliveryForm->handleRequest($request);

        if ($formCheckoutDeliveryForm->isSubmitted() && $formCheckoutDeliveryForm->isValid()) {

            // Delivery address creation
            $deliveryDetails['deliveryAddress'] = array("firstName" => $formCheckoutDeliveryForm['firstName']->getData(), "lastName" => $formCheckoutDeliveryForm['lastName']->getData(), "company" => $formCheckoutDeliveryForm['company']->getData(), "address1" => $formCheckoutDeliveryForm['address1']->getData(), "address2" => $formCheckoutDeliveryForm['address2']->getData(), "city" => $formCheckoutDeliveryForm['city']->getData(), "postcode" => $formCheckoutDeliveryForm['postcode']->getData(), "phone" => $formCheckoutDeliveryForm['phone']->getData());

            $deliveryDetails['comment'] = $formCheckoutDeliveryForm['comment']->getData();

            $session->set('deliveryDetails', $deliveryDetails);

            if (!$deliveryDetails) {
                return $this->redirectToRoute('checkout_delivery');
            } else {

                $postcode = $deliveryDetails['deliveryAddress']['postcode'];

                $deliveryRepository = $em->getRepository('AppBundle:Delivery')->findAll();

                $delivery = $deliveryRepository[0];
                $deliveryMultiple = $deliveryRepository[1];
                $deliverySurcharge = $deliveryRepository[2];

                $deliveryService = new DeliveryService();

                // Get Delivery and update basket
                $deliveryService->createDelivery($postcode, $delivery, $deliveryMultiple, $deliverySurcharge, $request);
            }

            return $this->redirectToRoute('checkout_review');

        }

        // Makes the products, products qty and the basket total available to the view if a basket exists

        $basketDetails = $session->get('basketDetails');
        $invoiceDetails = $session->get('invoiceDetails');
        $deliveryDetails = $session->get('deliveryDetails');

        if (!$basketDetails) {
            return $this->redirectToRoute('basket');
        }

        if (!$invoiceDetails) {
            return $this->redirectToRoute('checkout_invoice');
        }

        return $this->render('frontend/checkout-delivery.html.twig', array(
            'formCheckoutDeliveryForm' => $formCheckoutDeliveryForm->createView(),
            'basketDetails' => $basketDetails,
            'invoiceDetails' => $invoiceDetails,
            'deliveryDetails' => $deliveryDetails
        ));
    }

    /**
     * @Route("/checkout-review", name="checkout_review")
     */
    public function checkoutReviewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();

        // Makes the products, products qty and the basket total available to the view if a basket exists

        $basketDetails = $session->get('basketDetails');
        $invoiceDetails = $session->get('invoiceDetails');
        $deliveryDetails = $session->get('deliveryDetails');

        if (!$basketDetails) {
            return $this->redirectToRoute('basket');
        }

        if (!$invoiceDetails) {
            return $this->redirectToRoute('checkout_invoice');
        }

        if (!$deliveryDetails) {
            return $this->redirectToRoute('checkout_delivery');
        } else {

            $postcode = $deliveryDetails['deliveryAddress']['postcode'];

            $deliveryRepository = $em->getRepository('AppBundle:Delivery')->findAll();

            $delivery = $deliveryRepository[0];
            $deliveryMultiple = $deliveryRepository[1];
            $deliverySurcharge = $deliveryRepository[2];

            $deliveryService = new DeliveryService();

            // Get Delivery and update basket
            $deliveryService->createDelivery($postcode, $delivery, $deliveryMultiple, $deliverySurcharge, $request);
        }

        return $this->render('frontend/checkout-review.html.twig', array(
            'basketDetails' => $basketDetails,
            'invoiceDetails' => $invoiceDetails,
            'deliveryDetails' => $deliveryDetails
        ));
    }

    /**
     * @Route("/checkout-order", name="checkout_order")
     */
    public function checkoutOrderPlacedAction(Request $request)
    {
        $session = $request->getSession();

        // Makes the products, products qty and the basket total available to the view if a basket exists

        $basketDetails = $session->get('basketDetails');
        $invoiceDetails = $session->get('invoiceDetails');
        $deliveryDetails = $session->get('deliveryDetails');
        $merchantSession = $session->get('merchantSession');

        if (!$basketDetails) {
            return $this->redirectToRoute('basket');
        }

        if (!$invoiceDetails) {
            return $this->redirectToRoute('checkout_invoice');
        }

        if (!$deliveryDetails) {
            return $this->redirectToRoute('checkout_delivery');
        }

        if (!$merchantSession) {
            return $this->redirectToRoute('checkout_review');
        }

        $cardIdentifier = $request->query->get('cardIdentifier');

        $session->set('cardIdentifier', $cardIdentifier);

        $curl = curl_init();

        $str = $this->getParameter('sagepay_key') . ":" . $this->getParameter('sagepay_password');

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pi-live.sagepay.com/api/v1/transactions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{' .
                '"transactionType": "Payment",' .
                '"paymentMethod": {' .
                '    "card": {' .
                '        "merchantSessionKey": "' . $merchantSession . '",' .
                '        "cardIdentifier": "' . $cardIdentifier . '"' .
                '    }' .
                '},' .
                '"vendorTxCode": "demotransaction' . time() . '",' .
                '"amount": ' . intval(($basketDetails["deliveryAmount"] + $basketDetails["basketTotal"]) * 120) .',' .
                '"currency": "GBP",' .
                '"description": "SKD",' .
                '"apply3DSecure": "UseMSPSetting",' .
                '"customerFirstName": "' . $invoiceDetails['invoiceAddress']["firstName"] . '",' .
                '"customerLastName": "' . $invoiceDetails['invoiceAddress']["lastName"] . '",' .
                '"billingAddress": {' .
                '    "address1": "' . $invoiceDetails['invoiceAddress']["address1"] . '",' .
                '    "city": "' . $invoiceDetails['invoiceAddress']["city"] . '",' .
                '    "postalCode": "' . $invoiceDetails['invoiceAddress']["postcode"] . '",' .
                '    "country": "GB"' .
                '},' .
                '"entryMethod": "Ecommerce"' .
                '}',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . base64_encode($str),
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        $response = json_decode($response, true);

        curl_close($curl);

        if ($response["status"] != 'Ok') {

            $order = new Order();
            $order->setCreatedAt(new \DateTime("now"));
            $order->setEmail($invoiceDetails["email"]);
            $order->setFirstName($invoiceDetails["invoiceAddress"]["firstName"]);
            $order->setLastName($invoiceDetails["invoiceAddress"]["lastName"]);
            $order->setComment($deliveryDetails["comment"]);
            $order->setInvoiceAddress(json_encode($invoiceDetails["invoiceAddress"]));
            $order->setDeliveryAddress(json_encode($deliveryDetails["deliveryAddress"]));
            $order->setOrderDescription(json_encode($basketDetails["basketProducts"]));
            $order->setOrderAmount($basketDetails["basketTotal"]);
            $order->setDeliveryAmount($basketDetails["deliveryAmount"]);
            $order->setCardIdentifier($cardIdentifier);

            $em = $this->getDoctrine()->getManager();

            /** @var Status $status */
            $status = $em->getRepository('AppBundle:Status')
                ->findOneBy(array(
                    'id' => 3
                ));

            $order->setStatus($status);

            if ($invoiceDetails["purchaseOrderNumber"]) {
                $order->setPurchaseOrderNumber($invoiceDetails["purchaseOrderNumber"]);
            }

            if ($this->getUser()) {

                $user = $this->getUser();
                $order->setUser($user);

                $user->getDeliveryAddress();

                if ($invoiceDetails["invoiceAddress"] != $deliveryDetails["deliveryAddress"]) {

                    for ($x = 0; $x < count($user->getDeliveryAddress()); $x++) {
                        if ($user->getDeliveryAddress()[$x] != $deliveryDetails["deliveryAddress"]) {

                            $newDeliveryAddress = $deliveryDetails["deliveryAddress"];
                            $oldDeliveryAddresses = $user->getDeliveryAddress();
                            $oldDeliveryAddresses[count($user->getDeliveryAddress())] = $newDeliveryAddress;
                            $user->setDeliveryAddress($oldDeliveryAddresses);

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($user);
                            $em->flush();

                            break;
                        }
                    }
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            if (isset($response["errors"])) {

                //Send email to OPEC, confirmation of new order
                $messageCustomer = \Swift_Message::newInstance()
                    ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . ' - Error Code:' . $response["errors"][0]["code"] . ' - Details:' . $response["statusDetail"])
                    ->setFrom($this->getParameter('company_email'))
                    ->setTo($this->getParameter('company_email'))
                    ->setBody(
                        $this->renderView(
                            'email/newOrder.html.twig',
                            array(
                                'order' => $order
                            )
                        ),
                        'text/html'
                    );
                $this->get('mailer')->send($messageCustomer);

                //Send email to OPEC, confirmation of new order
                $messageCustomer = \Swift_Message::newInstance()
                    ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . ' - Error Code:' . $response["errors"][0]["code"] . ' - Details:' . $response["statusDetail"])
                    ->setFrom($this->getParameter('company_email'))
                    ->setTo($this->getParameter('admin_email'))
                    ->setBody(
                        $this->renderView(
                            'email/newOrder.html.twig',
                            array(
                                'order' => $order
                            )
                        ),
                        'text/html'
                    );
                $this->get('mailer')->send($messageCustomer);

            } else {

                //Send email to OPEC, confirmation of new order
                $messageCustomer = \Swift_Message::newInstance()
                    ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . ' - Error Code:' . $response["statusCode"] . ' - Details:' . $response["statusDetail"])
                    ->setFrom($this->getParameter('company_email'))
                    ->setTo($this->getParameter('company_email'))
                    ->setBody(
                        $this->renderView(
                            'email/newOrder.html.twig',
                            array(
                                'order' => $order
                            )
                        ),
                        'text/html'
                    );
                $this->get('mailer')->send($messageCustomer);

                $messageCustomer = \Swift_Message::newInstance()
                    ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . ' - Error Code:' . $response["statusCode"] . ' - Details:' . $response["statusDetail"])
                    ->setFrom($this->getParameter('company_email'))
                    ->setTo($this->getParameter('admin_email'))
                    ->setBody(
                        $this->renderView(
                            'email/newOrder.html.twig',
                            array(
                                'order' => $order
                            )
                        ),
                        'text/html'
                    );
                $this->get('mailer')->send($messageCustomer);

            }

            // Get all dropshipper
            /** @var Dropshipper $dropshipper */
            $dropshipper = $em->getRepository('AppBundle:Dropshipper')
                ->findAll();

            // Loop over each dropshipper
            foreach($dropshipper as $key => $value) {

                $dropshipperProducts = [];

                // Checking each product in basket to see who is the dropshipper
                for ($x = 0; $x < (count($order->getOrderDescription())); $x++) {

                    /** @var Product $product */
                    $product = $em->getRepository('AppBundle:Product')
                        ->find($order->getOrderDescription()[$x]["id"]);

                    // If the product is part of this dropshipper then add it
                    if ($value->getId() == $product->getDropshipper()->getId()) {
                        $dropshipperProducts[$x] = $order->getOrderDescription()[$x];
                    }
                }

                if ($dropshipperProducts) {

                    if (!file_exists('/tmp/' . $this->getParameter('company_name_url_friendly') . '-drop-shipment' . $order->getId() . '.pdf')) {
                        $snappy = new Pdf('xvfb-run -- wkhtmltopdf');
                        $snappy->generateFromHtml(
                            $this->renderView(
                                'email/dropshipper.html.twig',
                                array(
                                    'dropshipperProducts' => array_values($dropshipperProducts),
                                    'order' => $order
                                )
                            ),
                            '/tmp/' . $this->getParameter('company_name_url_friendly') . '-drop-shipment' . $order->getId() . '.pdf'
                        );
                    }

                    // Send email to OPEC as COPY
                    $messageDrop = \Swift_Message::newInstance()
                        ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . 'Drop Shipment Order COPY')
                        ->setFrom($this->getParameter('company_email'))
                        ->setTo($this->getParameter('company_email'))
                        ->setBody(
                            $this->renderView(
                                'email/dropshipper.html.twig',
                                array(
                                    'dropshipperProducts' => array_values($dropshipperProducts),
                                    'order' => $order
                                )
                            ),
                            'text/html'
                        )
                        ->attach(\Swift_Attachment::fromPath("/tmp/' . $this->getParameter('company_name_url_friendly') . '-shipment" . $order->getId() . ".pdf"))
                    ;
                    $this->get('mailer')->send($messageDrop);

                    $messageDrop = \Swift_Message::newInstance()
                        ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . 'Drop Shipment Order COPY')
                        ->setFrom($this->getParameter('company_email'))
                        ->setTo($this->getParameter('admin_email'))
                        ->setBody(
                            $this->renderView(
                                'email/dropshipper.html.twig',
                                array(
                                    'dropshipperProducts' => array_values($dropshipperProducts),
                                    'order' => $order
                                )
                            ),
                            'text/html'
                        )
                        ->attach(\Swift_Attachment::fromPath("/tmp/' . $this->getParameter('company_name_url_friendly') . '-drop-shipment" . $order->getId() . ".pdf"))
                    ;
                    $this->get('mailer')->send($messageDrop);

                }
            }

            $this->addFlash('warning', 'There was a problem with your transaction, please call us.');
            return $this->redirectToRoute('checkout_review');
        }

        $order = new Order();
        $order->setCreatedAt(new \DateTime("now"));
        $order->setEmail($invoiceDetails["email"]);
        $order->setFirstName($invoiceDetails["invoiceAddress"]["firstName"]);
        $order->setLastName($invoiceDetails["invoiceAddress"]["lastName"]);
        $order->setComment($deliveryDetails["comment"]);
        $order->setInvoiceAddress(json_encode($invoiceDetails["invoiceAddress"]));
        $order->setDeliveryAddress(json_encode($deliveryDetails["deliveryAddress"]));
        $order->setOrderDescription(json_encode($basketDetails["basketProducts"]));
        $order->setOrderAmount($basketDetails["basketTotal"]);
        $order->setDeliveryAmount($basketDetails["deliveryAmount"]);
        $order->setCardIdentifier($cardIdentifier);

        $em = $this->getDoctrine()->getManager();

        /** @var Status $status */
        $status = $em->getRepository('AppBundle:Status')
            ->findOneBy(array(
                'id' => 1
            ));

        $order->setStatus($status);

        if ($invoiceDetails["purchaseOrderNumber"]) {
            $order->setPurchaseOrderNumber($invoiceDetails["purchaseOrderNumber"]);
        }

        if ($this->getUser()) {

            $user = $this->getUser();
            $order->setUser($user);

            $user->getDeliveryAddress();

            if ($invoiceDetails["invoiceAddress"] != $deliveryDetails["deliveryAddress"]) {

                for ($x = 0; $x < count($user->getDeliveryAddress()); $x++) {
                    if ($user->getDeliveryAddress()[$x] != $deliveryDetails["deliveryAddress"]) {

                        $newDeliveryAddress = $deliveryDetails["deliveryAddress"];
                        $oldDeliveryAddresses = $user->getDeliveryAddress();
                        $oldDeliveryAddresses[count($user->getDeliveryAddress())] = $newDeliveryAddress;
                        $user->setDeliveryAddress($oldDeliveryAddresses);

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($user);
                        $em->flush();

                        break;
                    }
                }
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        //Send email to customer, confirmation of new order
        $messageCustomer = \Swift_Message::newInstance()
            ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name'))
            ->setFrom($this->getParameter('company_email'))
            ->setTo($invoiceDetails["email"])
            ->setBody(
                $this->renderView(
                    'email/newOrder.html.twig',
                    array(
                        'order' => $order
                    )
                ),
                'text/html'
            );
        $this->get('mailer')->send($messageCustomer);

        //Send email to OPEC, confirmation of new order
        $messageCustomer = \Swift_Message::newInstance()
            ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . ' COPY')
            ->setFrom($this->getParameter('company_email'))
            ->setTo($this->getParameter('company_email'))
            ->setBody(
                $this->renderView(
                    'email/newOrder.html.twig',
                    array(
                        'order' => $order
                    )
                ),
                'text/html'
            );
        $this->get('mailer')->send($messageCustomer);

        $messageCustomer = \Swift_Message::newInstance()
            ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . ' COPY')
            ->setFrom($this->getParameter('company_email'))
            ->setTo($this->getParameter('admin_email'))
            ->setBody(
                $this->renderView(
                    'email/newOrder.html.twig',
                    array(
                        'order' => $order
                    )
                ),
                'text/html'
            );
        $this->get('mailer')->send($messageCustomer);

        // Get all dropshipper
        /** @var Dropshipper $dropshipper */
        $dropshipper = $em->getRepository('AppBundle:Dropshipper')
            ->findAll();

        // Loop over each dropshipper
        foreach($dropshipper as $key => $value) {

            $dropshipperProducts = [];

            // Checking each product in basket to see who is the dropshipper
            for ($x = 0; $x < (count($order->getOrderDescription())); $x++) {

                /** @var Product $product */
                $product = $em->getRepository('AppBundle:Product')
                    ->find($order->getOrderDescription()[$x]["id"]);

                // If the product is part of this dropshipper then add it
                if ($value->getId() == $product->getDropshipper()->getId()) {
                    $dropshipperProducts[$x] = $order->getOrderDescription()[$x];
                }
            }

            if ($dropshipperProducts) {

                if (!file_exists('/tmp/' . $this->getParameter('company_name_url_friendly') . '-drop-shipment' . $order->getId() . '.pdf')) {
                    $snappy = new Pdf('xvfb-run -- wkhtmltopdf');
                    $snappy->generateFromHtml(
                        $this->renderView(
                            'email/dropshipper.html.twig',
                            array(
                                'dropshipperProducts' => array_values($dropshipperProducts),
                                'order' => $order
                            )
                        ),
                        '/tmp/' . $this->getParameter('company_name_url_friendly') . '-drop-shipment' . $order->getId() . '.pdf'
                    );
                }

                // Send email to dropshipper with the products they need to send
                $messageDrop = \Swift_Message::newInstance()
                    ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . 'Drop Shipment Order')
                    ->setFrom($this->getParameter('company_email'))
                    ->setTo($value->getEmail())
                    ->setBody(
                        $this->renderView(
                            'email/dropshipper.html.twig',
                            array(
                                'dropshipperProducts' => array_values($dropshipperProducts),
                                'order' => $order
                            )
                        ),
                        'text/html'
                    )
                    ->attach(\Swift_Attachment::fromPath("/tmp/' . $this->getParameter('company_name_url_friendly') . '-drop-shipment" . $order->getId() . ".pdf"))
                ;
                $this->get('mailer')->send($messageDrop);

                // Send email to OPEC as COPY
                $messageDrop = \Swift_Message::newInstance()
                    ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . 'Drop Shipment Order COPY')
                    ->setFrom($this->getParameter('company_email'))
                    ->setTo($this->getParameter('company_email'))
                    ->setBody(
                        $this->renderView(
                            'email/dropshipper.html.twig',
                            array(
                                'dropshipperProducts' => array_values($dropshipperProducts),
                                'order' => $order
                            )
                        ),
                        'text/html'
                    )
                    ->attach(\Swift_Attachment::fromPath("/tmp/' . $this->getParameter('company_name_url_friendly') . '-drop-shipment" . $order->getId() . ".pdf"))
                ;
                $this->get('mailer')->send($messageDrop);

                $messageDrop = \Swift_Message::newInstance()
                    ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . 'Drop Shipment Order COPY')
                    ->setFrom($this->getParameter('company_email'))
                    ->setTo('pill-kit-direct@connectjames.com')
                    ->setBody(
                        $this->renderView(
                            'email/dropshipper.html.twig',
                            array(
                                'dropshipperProducts' => array_values($dropshipperProducts),
                                'order' => $order
                            )
                        ),
                        'text/html'
                    )
                    ->attach(\Swift_Attachment::fromPath("/tmp/' . $this->getParameter('company_name_url_friendly') . '-drop-shipment" . $order->getId() . ".pdf"))
                ;
                $this->get('mailer')->send($messageDrop);

            }
        }

        // Make basket empty by making the basket in session NULL
        $session->set('basket', Null);
        $session->set('basketProducts', Null);
        $session->set('basketQty', Null);
        $session->set('basketTotal', Null);
        $session->set('delivery', Null);

        return $this->render('frontend/order-placed.html.twig', array(
            'order' => $order
        ));
    }

    /**
     * @Route("/checkout-order-without-payment", name="checkout_order_without_payment")
     */
    public function checkoutOrderPlacedWithoutPaymentAction(Request $request)
    {
        $session = $request->getSession();

        // Makes the products, products qty and the basket total available to the view if a basket exists

        $basketDetails = $session->get('basketDetails');
        $invoiceDetails = $session->get('invoiceDetails');
        $deliveryDetails = $session->get('deliveryDetails');

        if (!$basketDetails) {
            return $this->redirectToRoute('basket');
        }

        if (!$invoiceDetails) {
            return $this->redirectToRoute('checkout_invoice');
        }

        if (!$deliveryDetails) {
            return $this->redirectToRoute('checkout_delivery');
        }

        $order = new Order();
        $order->setCreatedAt(new \DateTime("now"));
        $order->setEmail($invoiceDetails["email"]);
        $order->setFirstName($invoiceDetails["invoiceAddress"]["firstName"]);
        $order->setLastName($invoiceDetails["invoiceAddress"]["lastName"]);
        $order->setComment($deliveryDetails["comment"]);
        $order->setInvoiceAddress(json_encode($invoiceDetails["invoiceAddress"]));
        $order->setDeliveryAddress(json_encode($deliveryDetails["deliveryAddress"]));
        $order->setOrderDescription(json_encode($basketDetails["basketProducts"]));
        $order->setOrderAmount($basketDetails["basketTotal"]);
        $order->setDeliveryAmount($basketDetails["deliveryAmount"]);

        $em = $this->getDoctrine()->getManager();

        /** @var Status $status */
        $status = $em->getRepository('AppBundle:Status')
            ->findOneBy(array(
                'id' => 2
            ));

        $order->setStatus($status);

        if ($invoiceDetails["purchaseOrderNumber"]) {
            $order->setPurchaseOrderNumber($invoiceDetails["purchaseOrderNumber"]);
        }

        if ($this->getUser()) {

            $user = $this->getUser();
            $order->setUser($user);

            $user->getDeliveryAddress();

            if ($invoiceDetails["invoiceAddress"] != $deliveryDetails["deliveryAddress"]) {

                for ($x = 0; $x < count($user->getDeliveryAddress()); $x++) {
                    if ($user->getDeliveryAddress()[$x] != $deliveryDetails["deliveryAddress"]) {

                        $newDeliveryAddress = $deliveryDetails["deliveryAddress"];
                        $oldDeliveryAddresses = $user->getDeliveryAddress();
                        $oldDeliveryAddresses[count($user->getDeliveryAddress())] = $newDeliveryAddress;
                        $user->setDeliveryAddress($oldDeliveryAddresses);

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($user);
                        $em->flush();

                        break;
                    }
                }
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        //Send email to customer, confirmation of new order
        $messageCustomer = \Swift_Message::newInstance()
            ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name'))
            ->setFrom($this->getParameter('company_email'))
            ->setTo($invoiceDetails["email"])
            ->setBody(
                $this->renderView(
                    'email/newOrder.html.twig',
                    array(
                        'order' => $order
                    )
                ),
                'text/html'
            );
        $this->get('mailer')->send($messageCustomer);

        //Send email to OPEC, confirmation of new order
        $messageCustomer = \Swift_Message::newInstance()
            ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . ' CONTACT REQUIRED')
            ->setFrom($this->getParameter('company_email'))
            ->setTo($this->getParameter('company_email'))
            ->setBody(
                $this->renderView(
                    'email/newOrder.html.twig',
                    array(
                        'order' => $order
                    )
                ),
                'text/html'
            );
        $this->get('mailer')->send($messageCustomer);

        $messageCustomer = \Swift_Message::newInstance()
            ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . ' CONTACT REQUIRED')
            ->setFrom($this->getParameter('company_email'))
            ->setTo($this->getParameter('admin_email'))
            ->setBody(
                $this->renderView(
                    'email/newOrder.html.twig',
                    array(
                        'order' => $order
                    )
                ),
                'text/html'
            );
        $this->get('mailer')->send($messageCustomer);

        // Get all dropshipper
        /** @var Dropshipper $dropshipper */
        $dropshipper = $em->getRepository('AppBundle:Dropshipper')
            ->findAll();

        // Loop over each dropshipper
        foreach($dropshipper as $key => $value) {

            $dropshipperProducts = [];

            // Checking each product in basket to see who is the dropshipper
            for ($x = 0; $x < (count($order->getOrderDescription())); $x++) {

                /** @var Product $product */
                $product = $em->getRepository('AppBundle:Product')
                    ->find($order->getOrderDescription()[$x]["id"]);

                // If the product is part of this dropshipper then add it
                if ($value->getId() == $product->getDropshipper()->getId()) {
                    $dropshipperProducts[$x] = $order->getOrderDescription()[$x];
                }
            }

            if ($dropshipperProducts) {

                if (!file_exists('/tmp/' . $this->getParameter('company_name_url_friendly') . '-shipment' . $order->getId() . '.pdf')) {
                    $snappy = new Pdf('xvfb-run -- wkhtmltopdf');
                    $snappy->generateFromHtml(
                        $this->renderView(
                            'email/dropshipper.html.twig',
                            array(
                                'dropshipperProducts' => array_values($dropshipperProducts),
                                'order' => $order
                            )
                        ),
                        '/tmp/' . $this->getParameter('company_name_url_friendly') . '-shipment' . $order->getId() . '.pdf'
                    );
                }

                // Send email to OPEC as COPY
                $messageDrop = \Swift_Message::newInstance()
                    ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . 'Drop Shipment Order COPY')
                    ->setFrom($this->getParameter('company_email'))
                    ->setTo($this->getParameter('company_email'))
                    ->setBody(
                        $this->renderView(
                            'email/dropshipper.html.twig',
                            array(
                                'dropshipperProducts' => array_values($dropshipperProducts),
                                'order' => $order
                            )
                        ),
                        'text/html'
                    )
                    ->attach(\Swift_Attachment::fromPath("/tmp/' . $this->getParameter('company_name_url_friendly') . '-drop-shipment" . $order->getId() . ".pdf"))
                ;
                $this->get('mailer')->send($messageDrop);

                $messageDrop = \Swift_Message::newInstance()
                    ->setSubject('Confirmation of your order from ' . $this->getParameter('company_name') . 'Drop Shipment Order COPY')
                    ->setFrom($this->getParameter('company_email'))
                    ->setTo($this->getParameter('admin_email'))
                    ->setBody(
                        $this->renderView(
                            'email/dropshipper.html.twig',
                            array(
                                'dropshipperProducts' => array_values($dropshipperProducts),
                                'order' => $order
                            )
                        ),
                        'text/html'
                    )
                    ->attach(\Swift_Attachment::fromPath("/tmp/' . $this->getParameter('company_name_url_friendly') . '-drop-shipment" . $order->getId() . ".pdf"))
                ;
                $this->get('mailer')->send($messageDrop);

            }
        }

        // Make basket empty by making the basket in session NULL
        $session->set('basket', Null);
        $session->set('basketProducts', Null);
        $session->set('basketQty', Null);
        $session->set('basketTotal', Null);
        $session->set('delivery', Null);

        return $this->render('frontend/order-placed.html.twig', array(
            'order' => $order
        ));
    }

    /**
     * @Route("/change-address/entered-new-address", name="change_address_entered_new_address")
     */
    public function changeAddressEnteredNewAddressAction(Request $request)
    {
        $session = $request->getSession();

        $invoiceDetails = $session->get('invoiceDetails');
        $deliveryDetails = $session->get('deliveryDetails');

        return $this->render(':frontend/checkout:_enteredNewAddress.html.twig', array(
            'invoiceDetails' => $invoiceDetails,
            'deliveryDetails' => $deliveryDetails
        ));
    }

    /**
     * @Route("/change-address/invoice-address", name="change_address_invoice_address")
     */
    public function changeAddressInvoiceAddressAction(Request $request)
    {
        $session = $request->getSession();

        $invoiceDetails = $session->get('invoiceDetails');

        return $this->render('frontend/checkout/_invoiceAddress.html.twig', array(
            'invoiceDetails' => $invoiceDetails
        ));
    }

    /**
     * @Route("/change-address/saved-address/{id}", name="change_address_saved_address")
     */
    public function changeAddressSavedAddressAction($id)
    {
        return $this->render('frontend/checkout/_savedAddress.html.twig', array(
            'id' => $id
        ));
    }

    /**
     * @Route("/generate-merchant-session", name="generate_merchant_session")
     */
    public function generateMerchantSessionAction(Request $request)
    {
        $session = $request->getSession();

        $curl = curl_init();

        $str = $this->getParameter('sagepay_key') . ":" . $this->getParameter('sagepay_password');

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pi-live.sagepay.com/api/v1/merchant-session-keys",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{ "vendorName":"' . $this->getParameter('sagepay_vendor') . '"}',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . base64_encode($str),
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $merchantSession = json_decode($response, true);
        $merchantSession = $merchantSession['merchantSessionKey'];

        $session->set('merchantSession', $merchantSession);

        return $this->render('frontend/checkout/_merchantSession.html.twig', array(
            'merchantSession' => $merchantSession
        ));
    }
}