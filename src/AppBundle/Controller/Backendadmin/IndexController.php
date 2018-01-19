<?php

namespace AppBundle\Controller\Backendadmin;

use AppBundle\Entity\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */

class IndexController extends Controller
{
    /**
     * @Route("/", name="index_admin")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('AppBundle:Order')->createQueryBuilder('ord');

        $query = $queryBuilder->getQuery();

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator  = $this->get('knp_paginator');

        $order = $paginator->paginate(
            $query,
            1,
            30,
            array('defaultSortFieldName' => 'ord.createdAt', 'defaultSortDirection' => 'desc')
        );

        return $this->render('backendadmin/index.html.twig', array(
            'orders' => $order
        ));
    }

}

