<?php

namespace AppBundle\Controller\Backendadmin;

use AppBundle\Entity\User;
use AppBundle\Form\UserForgotPassword;
use AppBundle\Service\TokenService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */

class ClientController extends Controller
{
    /**
     * @Route("/clients", name="clients")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();

        $queryBuilder = $em->getRepository('AppBundle:User')->createQueryBuilder('user');

        $query = $queryBuilder->getQuery();

        $request->query->getAlnum('records');

        // Can be changed by the user in the view, if requested then changes records number
        if ($request->query->getAlnum('records')) {
            $limit = $request->query->getAlnum('records');
            $session->set('limit', $limit);
        } elseif ($session->get('limit')) {
            $limit = $session->get('limit');
        } else {
            $limit = 15;
        }

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator  = $this->get('knp_paginator');

        $users = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', $limit),
            array('defaultSortFieldName' => 'user.lastName', 'defaultSortDirection' => 'asc')
        );

        return $this->render('backendadmin/client.html.twig', array(
            'users' => $users,
            'page' => $limit
        ));
    }

    /**
     * @Route("/clients/view/{id}", name="clients_view")
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $userRepository = $em->getRepository('AppBundle:User');

        // Find the order the admin wants to view

        /** @var User $user */
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('user not found');
        }

        // Check if an order after and before exist and make their Id available to the view
        $nextUser = 0;
        $previousUser = 0;

        if ($userRepository->findOneBy(
            array('id' => ($id - 1))
        )) {
            $previousUser = $id - 1;
        }

        if ($userRepository->findOneBy(
            array('id' => ($id + 1))
        )) {
            $nextUser = $id + 1;
        }

        return $this->render('backendadmin/viewClient.html.twig', array(
            'user' => $user,
            'previousClient' => $previousUser,
            'nextClient' => $nextUser,
        ));
    }

    /**
     * @Route("/clients/edit/{id}", name="clients_edit")
     */
    public function editAction($id, Request $request)
    {
        // Find the order the user wants to view
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(array(
                'id' => $id
            ));

        // If user edits the invoice address
        if ($request->query->get('invoiceAddress')) {

            $user->setEmail($request->query->get('email'));

            $user->setInvoiceAddress($request->query->get('invoiceAddress'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // If user edits the delivery address
        } elseif ($request->query->get('deliveryAddress')) {

            $oldDeliveryAddresses = $user->getDeliveryAddress();
            $arrayPosition = intval($request->query->get('arrayPosition'));
            $newDeliveryAddress = json_decode($request->query->get('deliveryAddress'), true);
            $oldDeliveryAddresses[$arrayPosition] = $newDeliveryAddress;

            $user->setDeliveryAddress($oldDeliveryAddresses);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('clients_view', array('id' => $id));
    }

    /**
     * @Route("/clients/delete/{id}", name="clients_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository('AppBundle:User')
            ->find($id);

        if (!$user) {
            throw $this->createNotFoundException('user not found');
        }

        $em->remove($user);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * @Route("/clients/reset/{id}/{token}", name="clients_reset_password")
     */
    public function resetPasswordAction($id, $token, Request $request)
    {
        // Find the order the user wants to view
        $em = $this->getDoctrine()->getManager();

        if ($token) {

            /** @var User $user */
            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array(
                    'id' => $id
                ));

            $formForgotPassword = $this->createForm(UserForgotPassword::class);

            if ($user) {

                $formForgotPassword->handleRequest($request);

                if ($formForgotPassword->isValid()) {

                    $userPassword = $formForgotPassword->getData();

                    $userPassword = $userPassword["plainPassword"];

                    $user->setPlainPassword($userPassword);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    $this->addFlash('success', 'Welcome back '.$user->getEmail());

                    return $this->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $user,
                            $request,
                            $this->get('app.security.login_form_authenticator'),
                            'main'
                        );
                }

                return $this->render(
                    'security/newPassword.html.twig',
                    array(
                        'formForgotPassword' => $formForgotPassword->createView(),
                        'user' => $user
                    )
                );
            } else {
                $this->addFlash('warning', 'This email is not registered with us or the token has expired');
                return $this->redirectToRoute('security_login');
            }

        } else if ($id) {

//            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
//            $newToken = '';
//            $max = strlen($characters) - 1;
//            for ($i = 0; $i < 100; $i++) {
//                $newToken .= $characters[mt_rand(0, $max)];
//            }

            $tokenService = new TokenService();
            $newToken = $tokenService->tokenCreation();

            /** @var User $user */
            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array(
                    'id' => $id
                ));

            if ($user) {
                $user->setToken($newToken);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Check it out! New Spill Kits Direct Website')
                    ->setFrom($this->getParameter('company_email'), $this->getParameter('company_name'))
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                            'email/newUserUnique.html.twig',
                            array(
                                'token' => $newToken,
                                'email' => $user->getEmail()
                            )

                        ),
                        'text/html'
                    )
                ;
                $this->get('mailer')->send($message);

                $this->addFlash('success', 'An email to the client has been sent to reset his/her password');
                return $this->redirectToRoute('clients');

            } else {

                $this->addFlash('info', 'This email is not registered with us');
                return $this->redirectToRoute('security_login');

            }

        } else {

            $this->addFlash('danger', 'Please enter an email in the "Email" field above');
            return $this->redirectToRoute('security_login');

        }
    }

    /**
     * @Route("/clients/change", name="clients_change_admin")
     */
    public function clientsAdminAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(array(
                'id' => $request->query->get('id')
            ));

        // If user exists
        if (!$user) {
            throw $this->createNotFoundException('Product not found');
        }

        // If user wants to change the admin status of the user
        if ($request->query->get('admin')) {

            if ($request->query->get('admin') == 2) {
                $user->setRoles(["ROLE_ADMIN"]);
                $em->persist($user);
                $em->flush();
            } else {
                $user->setRoles(["ROLE_USER"]);
                $em->persist($user);
                $em->flush();
            }

        }

        return $this->redirectToRoute('clients');
    }
}


