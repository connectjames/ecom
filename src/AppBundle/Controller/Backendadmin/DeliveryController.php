<?php

namespace AppBundle\Controller\Backendadmin;

use AppBundle\Entity\Delivery;
use AppBundle\Entity\Dropshipper;
use AppBundle\Entity\Surcharge;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */

class DeliveryController extends Controller
{
    /**
     * @Route("/deliveries", name="deliveries")
     */
    public function indexAction()
    {
        // Find the order the admin wants to view
        $em = $this->getDoctrine()->getManager();

        $deliveries = $em->getRepository('AppBundle:Delivery')->findAll();

        $dropshippers = $em->getRepository('AppBundle:Dropshipper')->findAll();

        $deliveryWeightsPrices = [];
        foreach ($deliveries as $delivery) {
            $deliveryWeightsPrices[$delivery->getId()] = json_decode($delivery->getAmount(), true);
        }

        return $this->render('backendadmin/delivery.html.twig', array(
            'deliveries' => $deliveries,
            'dropshippers' => $dropshippers,
            'deliveryWeightsPrices' => $deliveryWeightsPrices
        ));
    }

    /**
     * @Route("/deliveries-save/{id}", name="deliveries_save")
     */
    public function deliveriesSaveAction($id, Request $request)
    {
        // Find the delivery the admin wants to save
        $em = $this->getDoctrine()->getManager();

        /** @var Delivery $delivery */
        $delivery = $em->getRepository('AppBundle:Delivery')
            ->findOneBy(array(
                'id' => $id
            ));

        $delivery->setAmount($request->query->get('delivery'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($delivery);
        $em->flush();

        $deliveryWeightPrice = json_decode($delivery->getAmount(), true);

        return $this->render('backendadmin/delivery/_saveDelivery.html.twig', array(
            'delivery' => $delivery,
            'deliveryWeightPrice' => $deliveryWeightPrice
        ));
    }

    /**
     * @Route("/dropshippers-new", name="dropshippers_new")
     */
    public function dropshippersNewAction(Request $request)
    {
        // Find the delivery the admin wants to save
        $dropshipper = new Dropshipper();

        $dropshipper->setName($request->query->get('name'));
        $dropshipper->setEmail($request->query->get('email'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($dropshipper);
        $em->flush();

        return $this->redirectToRoute('deliveries');
    }

    /**
     * @Route("/dropshippers-save/{id}", name="dropshippers_save")
     */
    public function dropshippersSaveAction($id, Request $request)
    {
        // Find the delivery the admin wants to save
        $em = $this->getDoctrine()->getManager();

        /** @var Dropshipper $dropshipper */
        $dropshipper = $em->getRepository('AppBundle:Dropshipper')
            ->findOneBy(array(
                'id' => $id
            ));

        $dropshipper->setName($request->query->get('name'));
        $dropshipper->setEmail($request->query->get('email'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($dropshipper);
        $em->flush();

        return $this->redirectToRoute('deliveries');
    }

    /**
     * @Route("/dropshippers-delete/{id}", name="dropshippers_delete")
     */
    public function dropshippersDeleteAction($id)
    {
        // Find the delivery the admin wants to save
        $em = $this->getDoctrine()->getManager();

        /** @var Dropshipper $dropshipper */
        $dropshipper = $em->getRepository('AppBundle:Dropshipper')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$dropshipper) {
            throw $this->createNotFoundException('Dropshipper not found');
        }

        $em->remove($dropshipper);
        $em->flush();

        return new Response(null, 204);
    }
}


