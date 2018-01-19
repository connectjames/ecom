<?php

namespace AppBundle\Service;

use AppBundle\Entity\Order;
use AppBundle\Entity\Status;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrderStatusService extends Controller
{
    public function tokenCreation($id, $statusId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Order $order */
        $order = $em->getRepository('AppBundle:Order')
            ->findOneBy(array(
                'id' => $id
            ));

        // Check if order exists
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }


        // Switches the order to the right status
        switch ($statusId) {
            case 1:
                echo "Release payment and send message that the order is on the way to the customer " . $request->query->get('date');
                $order->setDispatchedAt(new \DateTime($request->query->get('date')));
                break;
            case 2:
                echo "Release payment";
                $order->setDispatchedAt(new \DateTime($request->query->get('date')));
                break;
            case 3:
                echo "Pending, nothing happens";
                break;
            case 4:
                echo "Block payment and send message about the transaction being blocked";
                break;
            case 5:
                echo "Abort payment and send message about the transaction being cancelled";
                break;
            default:
                $this->addFlash('warning', 'Status not found!');
        }

        /** @var Status $status */
        $status = $em->getRepository('AppBundle:Status')
            ->findOneBy(array(
                'id' => $statusId
            ));

        $order->setStatus($status);
        $em->persist($order);
        $em->flush();
    }
}