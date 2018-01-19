<?php

namespace AppBundle\Controller\Backendadmin;

use AppBundle\Entity\NotFound;
use AppBundle\Entity\Order;
use AppBundle\Entity\Redirect;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\RedirectBundle\Form\Type\RedirectType;

/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */

class RedirectController extends Controller
{
    /**
     * @Route("/redirects", name="redirects")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('AppBundle:Redirect')->createQueryBuilder('red');

        $query = $queryBuilder->getQuery();

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator  = $this->get('knp_paginator');

        $redirect = $paginator->paginate(
            $query,
            1,
            30,
            array('defaultSortFieldName' => 'red.lastAccessed', 'defaultSortDirection' => 'desc')
        );

        return $this->render('backendadmin/redirect.html.twig', array(
            'redirects' => $redirect
        ));
    }

    /**
     * @Route("/redirects/view/{id}", name="redirects_view")
     */
    public function redirectsViewAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Redirect $redirect */
        $redirect = $em->getRepository('AppBundle:Redirect')
            ->findOneBy(array(
                'id' => $id
            ));

        $formRedirect = $this->createForm(RedirectType::class, $redirect);

        $formRedirect->handleRequest($request);

        if ($formRedirect->isValid()) {
            $this->getDoctrine()->getManager()->persist($formRedirect->getData());
            $this->getDoctrine()->getManager()->flush($redirect);

            return $this->redirectToRoute('redirects');
        }

        return $this->render('backendadmin/viewRedirect.html.twig', array(
            'redirect' => $redirect,
            'formRedirect' => $formRedirect->createView()
        ));
    }

    /**
     * @Route("/redirects/new", name="redirects_new")
     */
    public function redirectsNewAction(Request $request)
    {
        $formRedirect = $this->createForm(RedirectType::class);

        $formRedirect->handleRequest($request);

        if ($formRedirect->isValid()) {
            $this->getDoctrine()->getManager()->persist($formRedirect->getData());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('redirects');
        }

        return $this->render('backendadmin/newRedirect.html.twig', array(
            'formRedirect' => $formRedirect->createView()
        ));
    }

    /**
     * @Route("/redirects/delete/{id}", name="redirects_delete")
     */
    public function redirectsDeleteAction($id)
    {

    }
}

