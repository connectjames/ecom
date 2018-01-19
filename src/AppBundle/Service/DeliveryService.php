<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DeliveryService extends Controller
{
    public function createDelivery($postcode, $delivery, $deliveryMultiple, $deliverySurcharge, $request)
    {
        $session = $request->getSession();

        $postcode = strtoupper($postcode);

        $session->set('deliveryEntry', $postcode);

        $postcode = $request->query->get('postcode');

        if (!$postcode) {
            $postcode = $session->get('deliveryPostcodeCalculate');
        }

        $session->set('deliveryPostcodeCalculate', $postcode);

        $basketProducts = $session->get('basketProducts');

        $totalWeight = 0;
        for ($x = 0; $x <= (count($basketProducts) - 1); $x++) {
            $totalWeight += $basketProducts[$x]["weight"] * $basketProducts[$x]["quantity"];
        }

        $deliveryAmount = json_decode($delivery->getAmount(), true);

        $standardDeliveryAmount = 0;
        foreach($deliveryAmount as $key => $value) {

            // Calculate amount to be paid for delivery
            if ($totalWeight <= intval($key)) {
                $standardDeliveryAmount = $value;
                break;
            }
        }

        // If weight over the maximum weight entered in the admin panel, then calculate per kg x price
        if (!$standardDeliveryAmount) {
            $deliveryMultipleAmount = json_decode($deliveryMultiple->getAmount(), true);

            $standardMultipleDeliveryAmount = 0;
            $standardDeliveryAmount = 0;
            foreach($deliveryMultipleAmount as $key => $value) {
                $standardMultipleDeliveryAmount = $key;
                $standardDeliveryAmount = $value;
                break;
            }

            $standardDeliveryAmount = $standardMultipleDeliveryAmount * $standardDeliveryAmount * $totalWeight;
        }

        $deliverySurchargeAmount = json_decode($deliverySurcharge->getAmount(), true);

        foreach($deliverySurchargeAmount as $key => $value) {
            $length = strlen($key);

            if (substr(strtoupper($postcode), 0, $length) === strtoupper($key)) {

                $session->set('surcharge', $key);

                $standardSurchargeDeliveryAmount = $value;

                $standardDeliveryAmount = $standardDeliveryAmount + $standardSurchargeDeliveryAmount;

                break;
            }
        }

        // If the total weight is 0 then free shipping
        if (!$totalWeight) {
            $standardDeliveryAmount = 0;
        }

        $session->set('delivery', $standardDeliveryAmount);

        $basketDetails = $session->get('basketDetails');

        if ($basketDetails) {
            $basketDetails["deliveryEntry"] = $session->get('deliveryEntry');
            $basketDetails["deliveryAmount"] = $session->get('delivery');
            $session->set('basketDetails', $basketDetails);
        }

        return;
    }
}