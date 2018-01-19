<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Service\BasketService;
use AppBundle\Service\Delivery;
use AppBundle\Service\DeliveryService;
use AppBundle\Service\Discount;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BasketController extends Controller
{
    /**
     * @Route("/basket", name="basket")
     */
    public function basketAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();

        if ($request->query->get('id')) {

            $id = $request->query->get('id');
            $quantity = $request->query->get('quantity');

            /** @var Product $product */
            $product = $em->getRepository('AppBundle:Product')
                ->findOneBy(array(
                    'id' => $id
                ));

            // If product exists
            if (!$product) {
                throw $this->createNotFoundException('Product not found');
            }

            $basketService = new BasketService();

            // Update Basket
            $basketService->updateBasket($product, $quantity, $request);

            $this->addFlash('success', 'Product updated in basket');

        }

        if ($request->query->get('id-remove')) {

            $basketService = new BasketService();

            $id = $request->query->get('id-remove');

            // Update Basket
            $basketService->deleteItemBasket($id, $request);

            $this->addFlash('success', 'Product deleted from basket');

        }

        if ($request->query->get('postcode')) {

            $postcode = $request->query->get('postcode');

            $deliveryRepository = $em->getRepository('AppBundle:Delivery')->findAll();

            $delivery = $deliveryRepository[0];
            $deliveryMultiple = $deliveryRepository[1];
            $deliverySurcharge = $deliveryRepository[2];

            $deliveryService = new DeliveryService();

            // Get Delivery and update basket
            $deliveryService->createDelivery($postcode, $delivery, $deliveryMultiple, $deliverySurcharge, $request);

            $this->addFlash('success', 'Delivery calculated');

        } else {

            $postcode = $session->get('deliveryEntry');

            $deliveryRepository = $em->getRepository('AppBundle:Delivery')->findAll();

            $delivery = $deliveryRepository[0];
            $deliveryMultiple = $deliveryRepository[1];
            $deliverySurcharge = $deliveryRepository[2];

            $deliveryService = new DeliveryService();

            // Get Delivery and update basket
            $deliveryService->createDelivery($postcode, $delivery, $deliveryMultiple, $deliverySurcharge, $request);

        }

        // Makes the products, products qty and the basket total available to the view if a basket exists
        $basketProducts = $session->get('basketProducts');
        $basketQty = $session->get('basketQty');
        $basketTotal = $session->get('basketTotal');
        $delivery = $session->get('delivery');

        $allRelatedProducts = [];

        if ($basketProducts) {
            foreach ($basketProducts as $basketProduct) {

                /** @var Product $product */
                $product = $em->getRepository('AppBundle:Product')
                    ->findOneBy(array(
                        'id' => $basketProduct['id']
                    ));

                $relatedProducts = $product->getParentProducts();

                $allRelatedProducts[$basketProduct['id']] = $relatedProducts;
            }
        }

        $basketDetails["basketProducts"] = $session->get('basketProducts');
        $basketDetails["basketQty"] = $session->get('basketQty');
        $basketDetails["basketTotal"] = $session->get('basketTotal');
        $basketDetails["deliveryEntry"] = $session->get('deliveryEntry');
        $basketDetails["deliveryAmount"] = $session->get('delivery');

        $session->set('basketDetails', $basketDetails);

        return $this->render('frontend/basket.html.twig', array(
            'basketProducts' => $basketProducts,
            'basketQty' => $basketQty,
            'basketTotal' => $basketTotal,
            'delivery' => $delivery,
            'allRelatedProducts' => $allRelatedProducts
        ));

    }

    /**
     * @Route("basket/update/product/delete-all", name="basket_empty")
     * @return RedirectResponse
     */
    public function basketUpdateProductDeleteAllAction(Request $request)
    {
        $session = $request->getSession();

        // Make basket empty by making the basket in session NULL
        $session->set('basket', Null);
        $session->set('basketProducts', Null);
        $session->set('basketQty', Null);
        $session->set('basketTotal', Null);
        $session->set('delivery', Null);

        $this->addFlash('success', 'All products have been deleted, your basket is empty!');

        // Redirect to index
        return $this->redirectToRoute('index');
    }
}