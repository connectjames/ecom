<?php

namespace AppBundle\Controller\Backendadmin;

use AppBundle\Entity\CategoryMenu;
use AppBundle\Entity\Dropshipper;
use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Form\ProductForm;
use AppBundle\Service\CatalogueService;
use AppBundle\Service\ImageService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\Image;

/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */

class ProductController extends Controller
{
    /**
     * @Route("/products", name="products")
     */
    public function productsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();

        $queryBuilder = $em->getRepository('AppBundle:Product')->createQueryBuilder('prod');

        $query = $queryBuilder->getQuery();

        $request->query->getAlnum('records');

        // Can be changed by the user in the view, if requested then changes records number
        if ($request->query->getAlnum('records')) {
            $limit = $request->query->getAlnum('records');
            $session->set('limit', $limit);
        } elseif ($session->get('limit')) {
            $limit = $session->get('limit');
        } else {
            $limit = 200;
        }

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator  = $this->get('knp_paginator');

        $products = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', $limit),
            array('defaultSortFieldName' => 'prod.id', 'defaultSortDirection' => 'asc')
        );

        return $this->render('backendadmin/product.html.twig', array(
            'products' => $products,
            'page' => $limit
        ));
    }

    /**
     * @Route("/products/view/{id}", name="products_view")
     */
    public function productsViewAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository('AppBundle:Product');

        /** @var Product $product */
        $product = $productRepository->findOneBy(
            array('id' => $id));

        // If product exists
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $formProduct = $this->createForm(ProductForm::class, $product);

        $formProduct->handleRequest($request);

        if ($formProduct->isSubmitted() && $formProduct->isValid()) {

            // Saving new product

            if ($formProduct['image']->getData()){

                $imageSaver = new ImageService();

                $imageName = $imageSaver->imageSaveAction(
                    $formProduct['image']->getData(),
                    $formProduct['name']->getData(),
                    $this->getParameter('images_directory')
                );

                $product->setImageName($imageName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product updated');
        }

        $relatedProducts = $product->getParentProducts();

        // Check if a product after and before exits
        $nextProduct = 0;
        $previousProduct = 0;

        if ($productRepository->findOneBy(
            array('id' => ($id - 1))
        )) {
            $previousProduct = $id - 1;
        }

        if ($productRepository->findOneBy(
            array('id' => ($id + 1))
        )) {
            $nextProduct = $id + 1;
        }

        return $this->render('backendadmin/viewProduct.html.twig', array(
            'formProduct' => $formProduct->createView(),
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'previousProduct' => $previousProduct,
            'nextProduct' => $nextProduct
        ));
    }

    /**
     * @Route("/products/new", name="products_new")
     */
    public function productsNewAction(Request $request)
    {
        $product = new Product();

        $formProduct = $this->createForm(ProductForm::class, $product);

        $formProduct->handleRequest($request);

        if ($formProduct->isSubmitted() && $formProduct->isValid()) {

            // Saving new product

            if ($formProduct['image']->getData()){

                $imageSaver = new ImageService();

                $imageName = $imageSaver->imageSaveAction(
                    $formProduct['image']->getData(),
                    $formProduct['name']->getData(),
                    $this->getParameter('images_directory')
                );

                $product->setImageName($imageName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product created');

            return $this->redirectToRoute('products_view', array('id' => $product->getId()));
        }

        return $this->render('backendadmin/newProduct.html.twig', array(
            'formProduct' => $formProduct->createView()
        ));

    }

    /**
     * @Route("/products/delete/{id}", name="products_delete")
     */
    public function productsDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('AppBundle:Product')
            ->findOneBy(array(
                'id' => $id
            ));

        // If product exists
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/products/change", name="products_change_display_or_featured_product")
     */
    public function productsDisplayFeaturedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Product $product */
        $product = $em->getRepository('AppBundle:Product')
            ->findOneBy(array(
                'id' => $request->query->get('id')
            ));

        // If product exists
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        // If user wants to change the display status of the product
        if ($request->query->get('display')) {

            if ($request->query->get('display') == 2) {

                $product->setDisplay(0);
                $em->persist($product);
                $em->flush();
            } else {

                $product->setDisplay(1);
                $em->persist($product);
                $em->flush();
            }

            // If user wants to change the featured status of the product
        } elseif ($request->query->get('featured')) {

            if ($request->query->get('featured') == 2) {

                $product->setFeatured(0);
                $em->persist($product);
                $em->flush();
            } else {

                $product->setFeatured(1);
                $em->persist($product);
                $em->flush();
            }
        }

        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/products/changes", name="products_changes_display_product")
     */
    public function productsDisplaysAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Explode the products from ids separated by comma. ie: 1,2,3,4,5 to Array
        $ids = explode( ',', $request->query->get('productsId') );

        // Change each products to this new display status
        for ($x=0, $c=count($ids); $x<$c; $x++) {
            /** @var Product $product */
            $product = $em->getRepository('AppBundle:Product')
                ->findOneBy(array(
                    'id' => $ids[$x]
                ));

            // If products exists
            if (!$product) {
                throw $this->createNotFoundException('product not found');
            }

            $product->setDisplay((int)($request->query->get('display') - 1));
            $em->persist($product);
            $em->flush();

        }

        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/products/price", name="products_change_price")
     */
    public function productsPriceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Product $product */
        $product = $em->getRepository('AppBundle:Product')
            ->findOneBy(array(
                'id' => $request->query->get('id')
            ));

        // If product exists
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        // If user wants to change the price of the product
        $product->setPrice(($request->query->get('price')));
        $em->persist($product);
        $em->flush();

        return $this->redirectToRoute('products');
    }
}


