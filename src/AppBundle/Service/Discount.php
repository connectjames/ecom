<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class Discount extends Controller
{

    public function updateWithoutUser($discountName, Request $request)
    {

        // Find the discount used in the basket
        $discount = $this->getDoctrine()
            ->getRepository('AppBundle:Discount')
            ->findOneBy(
                array('name' => $discountName)
            );

        // If discount is correct and is found, add it to the session
        if ($discount) {

            $session = $request->getSession();
            $session->set('discount', $discount->getDiscountPercentage());

        }

        // If discount is incorrect or correct to let know the controller
        return $discount;

    }

    public function updateWithUser($user, Request $request)
    {
        // Find the products in the basket
        $session = $request->getSession();
        $discountAmount = $user->getDiscount()->getDiscountPercentage();

        // Inserts the delivery Id and its amount in the session
        $session->set('discount', $discountAmount);

        return;

    }

}