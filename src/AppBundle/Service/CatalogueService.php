<?php

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CatalogueService extends Controller
{
    public function relatedProducts($em, $request)
    {
        $relatedProductsSelected = [];
        if ($request->query->get('checkboxes')) {
            $finalValues = explode(",", $request->query->get('checkboxes'));
            $relatedProductsSelected = [];
            for ($i=0, $c=count($finalValues); $i<$c; $i++) {
                $relatedProduct = $em->getRepository('AppBundle:Product')
                    ->findOneBy(array(
                        'id' => $finalValues[$i]
                    ));
                $relatedProductsSelected[$i] = $relatedProduct;
            }
        }

        return $relatedProductsSelected;
    }

    public function deleteAllRelatedProducts($em, $target, $all)
    {
        foreach ($all as $every) {
            $target->removeCategoryProduct($every);
            $every->removeCategoryProduct($target);
            $em->persist($every);
            $em->persist($target);
            $em->flush(array(
                $every,
                $target,
            ));
        }

        return;
    }

    public function addAllRelatedProducts($em, $target, $targeted, $array)
    {
        for ($j=0, $c=count($array); $j<$c; $j++) {

            /** @var Product $product */
            $product = $em->getRepository('AppBundle:' . $targeted . '')
                ->findOneBy(array(
                    'id' => $array[$j]
                ));

            $target->addCategoryProduct($product);
            $product->addCategoryProduct($target);

            $em->persist($product);
            $em->persist($target);
            $em->flush(array(
                $product,
                $target,
            ));

        }

        return;
    }

    public function arrayWithoutChildrenInMenu($childrenItem, $categoriesMenuRepOrdering)
    {
        $test1 = ',' . json_encode($childrenItem);
        $test2 = json_encode($childrenItem) . ',';
        if (strpos($categoriesMenuRepOrdering, $test1) !== false) {
            $categoriesMenuFinal = str_replace($test1,'',$categoriesMenuRepOrdering);
        } else if (strpos($categoriesMenuRepOrdering, $test2) !== false) {
            $categoriesMenuFinal = str_replace($test2,'',$categoriesMenuRepOrdering);
        } else {
            $test3 = str_replace(json_encode($childrenItem),'',$categoriesMenuRepOrdering);
            $categoriesMenuFinal = str_replace(',"children":[]}','}',$test3);
        }

        return $categoriesMenuFinal;
    }

}