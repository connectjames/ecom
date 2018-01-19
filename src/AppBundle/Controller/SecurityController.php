<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Entity\User;
use AppBundle\Form\LoginForm;
use AppBundle\Form\UserForgotPassword;
use AppBundle\Form\UserRegistrationForm;
use AppBundle\Service\TokenService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction(Request $request)
    {
        $formRegister = $this->createForm(UserRegistrationForm::class);

        $formRegister->handleRequest($request);

        if ($formRegister->isSubmitted() && $formRegister->isValid()) {

            // Invoice address creation
            $invoiceAddress = array(
                "firstName" => trim($formRegister->get('firstName')->getData()),
                "lastName" => trim($formRegister->get('lastName')->getData()),
                "company" => trim($formRegister->get('company')->getData()),
                "address1" => trim($formRegister['address1']->getData()),
                "address2" => trim($formRegister['address2']->getData()),
                "city" => trim($formRegister['city']->getData()),
                "postcode" => trim($formRegister['postcode']->getData()),
                "phone" => trim($formRegister['phone']->getData())
            );

            $user = new User();
            $user->setCreatedAt(new \DateTime("now"));
            $user->setFirstName($formRegister['firstName']->getData());
            $user->setLastName($formRegister['lastName']->getData());
            $user->setEmail($formRegister['email']->getData());
            $user->setInvoiceAddress(json_encode($invoiceAddress));
            $user->setPlainPassword($formRegister['plainPassword']->getData());
            $user->setNewsletter($formRegister['newsletter']->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            /** @var Order $order */
            $orders = $em->getRepository('AppBundle:Order')
                ->findBy(array(
                    'email' => $formRegister['email']->getData()
                ));

            if ($orders) {
                foreach ($orders as $order) {
                    $order->setUser($user);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($order);
                    $em->flush();
                }
            }

            $message = \Swift_Message::newInstance()
                ->setSubject('Registration Complete')
                ->setFrom($this->getParameter('company_email'), $this->getParameter('company_name'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'email/newAccount.html.twig',
                        array(
                            'user' => $user
                        )

                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            $message = \Swift_Message::newInstance()
                ->setSubject('New user')
                ->setFrom($this->getParameter('company_email'), $this->getParameter('company_name'))
                ->setTo($this->getParameter('company_email'))
                ->setBody('New user registration')
            ;

            $this->get('mailer')->send($message);

            $this->addFlash('success', 'Welcome '.$user->getEmail());

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $formLogin = $this->createForm(LoginForm::class, [
            '_username' => $lastUsername,
        ]);

        // Makes the products, products qty and the basket total available to the view if a basket exists
        $session = $request->getSession();

        $basketProducts = $session->get('basketProducts');
        $basketQty = $session->get('basketQty');
        $basketTotal = $session->get('basketTotal');

        return $this->render('security/login.html.twig',
            array(
                'formLogin' => $formLogin->createView(),
                'formRegister' => $formRegister->createView(),
                'error' => $error,
                'basketProducts' => $basketProducts,
                'basketQty' => $basketQty,
                'basketTotal' => $basketTotal
            )
        );
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('this should not be reached!');
    }

    /**
     * @Route("/retrieve-password/{email}/{token}", name="retrieve_password")
     */
    public function retrievePasswordAction($email, $token, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($token) {

            /** @var User $user */
            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array(
                    'email' => $email,
                    'token' => $token
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

                    $this->addFlash('success', 'Welcome back ' . $user->getFullName());

                    return $this->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $user,
                            $request,
                            $this->get('app.security.login_form_authenticator'),
                            'main'
                        );
                }

                // Makes the products, products qty and the basket total available to the view if a basket exists
                $session = $request->getSession();

                $basketProducts = $session->get('basketProducts');
                $basketQty = $session->get('basketQty');
                $basketTotal = $session->get('basketTotal');

                return $this->render(
                    'security/newPassword.html.twig',
                    array(
                        'formForgotPassword' => $formForgotPassword->createView(),
                        'user' => $user,
                        'basketProducts' => $basketProducts,
                        'basketQty' => $basketQty,
                        'basketTotal' => $basketTotal
                    )
                );
            } else {
                $this->addFlash('danger', 'This email is not registered with us or the token has expired');
                return $this->redirectToRoute('security_login');
            }

        } else if ($email) {

            $tokenService = new TokenService();
            $newToken = $tokenService->tokenCreation();

            /** @var User $user */
            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array(
                    'email' => $email
                ));

            if (!$user) {
                throw $this->createNotFoundException('email not found');
            }

            if ($user) {
                $user->setToken($newToken);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Forgot password?')
                    ->setFrom($this->getParameter('company_email'))
                    ->setTo($email)
                    ->setBody(
                        $this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                            'email/forgotPassword.html.twig',
                            array(
                                'token' => $newToken,
                                'email' => $email
                            )

                        ),
                        'text/html'
                    )
                ;
                $this->get('mailer')->send($message);

                $this->addFlash('success', 'An email has been sent through to your email address, don\'t forget to check your junk email!');
                return $this->redirectToRoute('security_login');

            } else {

                $this->addFlash('info', 'This email is not registered with us');
                return $this->redirectToRoute('security_login');

            }

        } else {

            $this->addFlash('danger', 'Please enter an email to retrieve your password...');
            return $this->redirectToRoute('security_login');

        }
    }
}