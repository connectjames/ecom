<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BasketService extends Controller
{
    public function createBasket($product, $quantity, $request)
    {
        $session = $request->getSession();

        $basketProducts = array
        (
            array(
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => $quantity,
                'imageName' => $product->getImageName(),
                'weight' => $product->getWeight(),
                'total' => $product->getPrice(),
                'url' => $product->getSlug(),
                'sku' => $product->getSku(),
            ),
        );

        $basketQty = $quantity;
        $basketTotal = $product->getPrice() * floatval($quantity);

        $session->set('basketProducts', $basketProducts);
        $session->set('basketQty', $basketQty);
        $session->set('basketTotal', $basketTotal);

        return;
    }

    public function addToBasket($product, $quantity, $request)
    {
        $session = $request->getSession();

        $basketProducts = $session->get('basketProducts');

        $productPresent = false;
        $basketQty = 0;
        $basketTotal = 0;

        // Checking if the product already exists in the basket and if yes, adds to it, if no then else
        for ($x = 0; $x <= (count($basketProducts) - 1); $x++) {
            if ($basketProducts[$x]["id"] == $product->getId()) {
                $basketProducts[$x]["quantity"] += $quantity;
                $basketProducts[$x]["total"] = $basketProducts[$x]["quantity"] * $basketProducts[$x]["price"];
                $productPresent = true;
            }
            $basketQty += $basketProducts[$x]["quantity"];
            $basketTotal += $basketProducts[$x]["total"];
        }

        if ($productPresent) {

            // The product was already in the basket and now that its qty is added, the basket can be put back in the session
            $session->set('basketProducts', $basketProducts);
            $session->set('basketQty', $basketQty);
            $session->set('basketTotal', $basketTotal);

        } else {

            // The product does not exist in the basket, adding a new array to the session
            $total = $product->getPrice() * floatval($quantity);

            $newBasketProduct = array(
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => floatval($product->getPrice()),
                'quantity' => $quantity,
                'imageName' => $product->getImageName(),
                'weight' => $product->getWeight(),
                'total' => $total,
                'url' => $product->getSlug(),
                'sku' => $product->getSku(),
            );

            $basketProducts[] = $newBasketProduct;
            $basketQty += $quantity;
            $basketTotal += $product->getPrice();
            $session->set('basketProducts', $basketProducts);
            $session->set('basketQty', $basketQty);
            $session->set('basketTotal', $basketTotal);
        }

        return;
    }

    public function updateBasket($product, $quantity, $request)
    {
        $session = $request->getSession();

        $basketProducts = $session->get('basketProducts');

        $basketQty = 0;
        $basketTotal = 0;

        // Checking if the product already exists in the basket and if yes, adds to it, if no then else
        for ($x = 0; $x <= (count($basketProducts) - 1); $x++) {
            if ($basketProducts[$x]["id"] == $product->getId()) {
                $basketProducts[$x]["quantity"] = $quantity;
                $basketProducts[$x]["total"] = $basketProducts[$x]["quantity"] * $basketProducts[$x]["price"];
            }
            $basketQty += $basketProducts[$x]["quantity"];
            $basketTotal += $basketProducts[$x]["total"];
        }

        // The product was already in the basket and now that its qty is added, the basket can be put back in the session
        $session->set('basketProducts', $basketProducts);
        $session->set('basketQty', $basketQty);
        $session->set('basketTotal', $basketTotal);
        return;
    }

    public function deleteItemBasket($id, $request)
    {
        $session = $request->getSession();

        $basketProducts = $session->get('basketProducts');

        for ($x = 0; $x <= (count($basketProducts) - 1); $x++) {
            if ($basketProducts[$x]["id"] == $id) {
                unset($basketProducts[$x]);
            }
        }

        $basketProducts = array_values($basketProducts);

        $session->set('basketProducts', $basketProducts);

        // Adding now the products of the basket (array), the basket quantity and the basket total to the session
        $basketContent = $session->get('basketProducts');

        $basketProducts = [];
        $basketQty = 0;
        $basketTotal = 0;

        for ($x = 0; $x <= (count($basketContent) - 1); $x++) {
            $basketProducts[] = $basketContent[$x];
            $basketQty += $basketContent[$x]["quantity"];
            $basketTotal += $basketContent[$x]["price"] * floatval($basketContent[$x]["quantity"]);
        }

        $session->set('basketProducts', $basketProducts);
        $session->set('basketQty', $basketQty);
        $session->set('basketTotal', $basketTotal);
        return;
    }
}