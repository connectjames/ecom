<?php

namespace AppBundle\Controller\Backendadmin;

use AppBundle\Entity\CategoryMenu;
use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Form\CategoryForm;
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

class CategoryController extends Controller
{

    /**
     * @Route("/categories", name="categories")
     */
    public function categoriesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Get all Category
        $categories = $em->getRepository('AppBundle:Category')->findAll();

        // Get all CategoryMenu
        $categoriesMenuRepository = $em->getRepository('AppBundle:CategoryMenu');

        // Find the ordering used for the menu
        $categoriesMenuRepository = $categoriesMenuRepository->findOneBy(
            array('name' => 'menu'));

        // If the ordering is saved from the view
        if ($request->query->get('categoriesMenu')) {

            // Check if json is valid
            $data = json_decode($request->query->get('categoriesMenu'), true);

            // If invalid
            if ($data === null) {
                $this->addFlash('warning', 'Main menu could not be updated because the returned menu is not a valid Json');
            }
            // If valid
            $categoriesMenuRepository->setOrdering($request->query->get('categoriesMenu'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($categoriesMenuRepository);
            $em->flush();

            $this->addFlash('success', 'Main menu updated');

        }

        // Create the array for ordering the categories in the view
        $categoriesMenuFinal = json_decode($categoriesMenuRepository->getOrdering(), true);

        return $this->render('backendadmin/category.html.twig', array(
            'categories' => $categories,
            'categoriesMenu' => $categoriesMenuFinal
        ));
    }

    /**
     * @Route("/categories/view/{id}", name="categories_view")
     */
    public function categoriesViewAction($id, Request $request)
    {
        // Find the category the admin wants to view
        $em = $this->getDoctrine()->getManager();
        $categoriesRepository = $em->getRepository('AppBundle:Category');

        /** @var Category $category */
        $category = $categoriesRepository->findOneBy(
            array('id' => $id));

        // For menu updated if some changes are made
        $categoryOldUrl = $category->getSlug();

        // If category exists
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $formCategory = $this->createForm(CategoryForm::class, $category);

        $formCategory->handleRequest($request);

        if ($formCategory->isSubmitted() && $formCategory->isValid()) {

            // Save category changes
            if ($formCategory['image']->getData()) {

                $imageSaver = new ImageService();

                $imageName = $imageSaver->imageSaveAction(
                    $formCategory['image']->getData(),
                    $formCategory['name']->getData(),
                    $this->getParameter('images_directory')
                );

                $category->setImageName($imageName);
            }

            $em->persist($category);
            $em->flush();

            /** @var CategoryMenu $categoryMenu */
            $categoryMenu = $em->getRepository('AppBundle:CategoryMenu')
                ->findOneBy(array(
                    'name' => "menu"
                ));
            $categoryMenu->setOrdering(str_replace($categoryOldUrl,$category->getSlug(),$categoryMenu->getOrdering()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($categoryMenu);
            $em->flush();


            $this->addFlash('success', 'Category updated');

            return $this->redirectToRoute('categories_view', array('id' => $category->getId()));

        }

        // Check if a category after and before exits
        $nextCategory = 0;
        $previousCategory = 0;

        // Previous category search
        if ($categoriesRepository->findOneBy(
            array('id' => ($id - 1))
        )) {
            $previousCategory = $id - 1;
        }

        // Next category search
        if ($categoriesRepository->findOneBy(
            array('id' => ($id + 1))
        )) {
            $nextCategory = $id + 1;
        }

        return $this->render('backendadmin/viewCategory.html.twig', array(
            'formCategory' => $formCategory->createView(),
            'category' => $category,
            'previousCategory' => $previousCategory,
            'nextCategory' => $nextCategory
        ));
    }

    /**
     * @Route("/categories/new", name="categories_new")
     */
    public function categoriesNewAction(Request $request)
    {
        // New category creation
        $category = new Category();

        $formCategory = $this->createForm(CategoryForm::class, $category);

        $formCategory->handleRequest($request);

        if ($formCategory->isSubmitted() && $formCategory->isValid()) {

            // Saving new category
            if ($formCategory['image']->getData()){

                $imageSaver = new ImageService();

                $imageName = $imageSaver->imageSaveAction(
                    $formCategory['image']->getData(),
                    $formCategory['name']->getData(),
                    $this->getParameter('images_directory')
                );

                $category->setImageName($imageName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            /** @var CategoryMenu $categoryMenu */
            $categoryMenu = $em->getRepository('AppBundle:CategoryMenu')
                ->findOneBy(array(
                    'name' => 'menu'
                ));
            $categoriesMenu = rtrim($categoryMenu->getOrdering(), ']');
            $categoriesMenu = $categoriesMenu . ',{"url":"' . $category->getSlug() . '"}]';

            $categoryMenu->setOrdering($categoriesMenu);
            $em->persist($categoryMenu);
            $em->flush();

            $this->addFlash('success', 'Category created');

            return $this->redirectToRoute('categories_view', array('id' => $category->getId()));

        }

        return $this->render('backendadmin/newCategory.html.twig', array(
            'formCategory' => $formCategory->createView()
        ));
    }

    /**
     * @Route("/categories/delete/{id}", name="categories_delete")
     */
    public function categoriesDeleteAction($id)
    {
        // Find the category the admin wants to view
        $em = $this->getDoctrine()->getManager();

        $categoriesMenuRepository = $em->getRepository('AppBundle:CategoryMenu');
        $categoriesRepository = $em->getRepository('AppBundle:Category');

        $catalogueService = new CatalogueService();

        // Find requested category
        /** @var Category $category */
        $category = $categoriesRepository->findOneBy(
            array('id' => $id));

        $categorySlug = $category->getSlug();

        $categoriesMenuRep = $categoriesMenuRepository->findOneBy(
            array('name' => 'menu'));
        $categoriesMenuRepOrdering = $categoriesMenuRep->getOrdering();

        $categoriesMenu = json_decode($categoriesMenuRepOrdering, true);

        $categoriesMenuFinal = [];

        // delete
        foreach ($categoriesMenu as $categoryMenu) {

            // array with children level 1
            if (count($categoryMenu) > 1 && is_array($categoryMenu["children"])) {
                if ($categoryMenu["url"] == $categorySlug) {
                    $testChild = ltrim(json_encode($categoryMenu["children"]), '[');
                    $testChild = rtrim($testChild, ']');
                    $categoriesMenuFinal = str_replace(json_encode($categoryMenu),$testChild,$categoriesMenuRepOrdering);
                }

                // array with children level 2
                foreach ($categoryMenu["children"] as $itemInChildren) {
                    if (count($itemInChildren) > 1 && is_array($itemInChildren["children"])) {
                        if ($itemInChildren["url"] == $categorySlug) {
                            $testChild = ltrim(json_encode($itemInChildren["children"]), '[');
                            $testChild = rtrim($testChild, ']');
                            $test1 = ',' . json_encode($itemInChildren);
                            $test2 = json_encode($itemInChildren) . ',';
                            if (strpos($categoriesMenuRepOrdering, $test1) !== false) {
                                $categoriesMenuFinal = str_replace($test1,',' . $testChild,$categoriesMenuRepOrdering);
                            } else if (strpos($categoriesMenuRepOrdering, $test2) !== false) {
                                $categoriesMenuFinal = str_replace($test2,$testChild . ',',$categoriesMenuRepOrdering);
                            } else {
                                $test3 = str_replace(json_encode($itemInChildren),'',$categoriesMenuRepOrdering);
                                $categoriesMenuFinal = str_replace(',"children":[]}','},' . $testChild,$test3);
                            }
                        }

                        // array with children level 3
                        foreach ($itemInChildren["children"] as $itemInChildren2) {

                            // array without children level 3
                            if ($itemInChildren2["url"] == $category->getSlug()) {

                                $childrenItem = $itemInChildren2;
                                $categoriesMenuFinal = $catalogueService->arrayWithoutChildrenInMenu($childrenItem, $categoriesMenuRepOrdering);
                            }

                        }

                    } else {

                        // array without children level 2
                        if ($itemInChildren["url"] == $category->getSlug()) {

                            $childrenItem = $itemInChildren;
                            $categoriesMenuFinal = $catalogueService->arrayWithoutChildrenInMenu($childrenItem, $categoriesMenuRepOrdering);
                        }

                    }
                }

            } else {

                // array without children level 1
                if ($categoryMenu["url"] == $category->getSlug()) {

                    $childrenItem = $categoryMenu;
                    $categoriesMenuFinal = $catalogueService->arrayWithoutChildrenInMenu($childrenItem, $categoriesMenuRepOrdering);
                }
            }
        }

        $categoriesMenuRep->setOrdering($categoriesMenuFinal);
        $em->persist($categoriesMenuRep);
        $em->flush();

        $this->addFlash('success', 'Category ' . $category->getName() . ' has been successfully deleted, if the category deleted had sub-level(s) they are now available at a lower level. Please check below.');

        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('categories');
    }

    /**
     * @Route("/categories/change", name="categories_change_display")
     */
    public function categoriesDisplayAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Product $product */
        $category = $em->getRepository('AppBundle:Category')
            ->findOneBy(array(
                'id' => $request->query->get('id')
            ));

        // If the category exists
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        // If user wants to change the display status of the category
        if ($request->query->get('display')) {

            if ($request->query->get('display') == 2) {

                $category->setDisplay(0);
                $em->persist($category);
                $em->flush();
            } else {

                $category->setDisplay(1);
                $em->persist($category);
                $em->flush();
            }
        }

        return $this->redirectToRoute('categories');
    }
}


