<?php
namespace App\Controller;
use App\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\frameworExtraBundle\Configuration\Metod;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
         $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/api/users", name="home")
     * Method({"GET"})
     */
    public function index() 
    {

        $user = $this->getDoctrine()->getRepository(User::class)->findAll();;
        return $this->render('user/index.html.twig', array('useres' => $user));

    }

    /**
     * @Route("/api/user/new", name="user_new")
     * Method({"GET", "POST"})
     */

    public function new (Request $request)
    {
        $user = new User();

        $form = $this->createFormBuilder( $user)
        ->add('email', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('password', TextareaType::class, array(
        'required' => false,
        'attr' => array('class' => 'form-control')
        ))
        ->add('roles', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('save', SubmitType::class, array(
        'label' => 'Register',
        'attr' => array('class' => 'btn btn-primary mt-3')
        ))
        ->getForm();



        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('user/new.html.twig', array( 'form' => $form->createView()));


    }

    /**
     * @Route("/api/user/{id}", name="user_show")
     */
    public function show($id) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        var_dump($user);

        return $this->render('user/show.html.twig', array('user' => $user));
    }

    /**
            * @Route("api/user/edit/{id}", name="edit_user")
            * Method({"GET", "POST"})
         */

        public function edit(Request $request, $id) {
            $user = new User();
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);


            $form = $this->createFormBuilder($user)
            ->add('email', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('password', TextareaType::class, array(
            'required' => false,
            'attr' => array('class' => 'form-control')
            ))
            ->add('roles', TextType::class, null, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array(
            'label' => 'Update',
            'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();
            
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                return $this->redirectToRoute('home');
            }

            return $this->render('user/edit.html.twig', array('form' => $form->createView()));


        }


    /**
     * @Route("/api/user/delete/{id}")
     * Method({"DELETE"})
     */    

    public function delete (Request $request, $id) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        // var_dump($user);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $response = new Response();
        $response->send();
     }


    // /**
    //  * @Route("/register/save")
    //  */
    // public function save()
    // {
    //     $entityManager = $this->getDoctrine()->getManager();

    //     $user = new User;
    //     $user->setEmail('javierysusamiguitos');
    //     $user->setRoles('mi cara cuando javier');
    //     $user->setPassword($this->passwordEncoder->encodePassword($user, '321'));

    //     $entityManager->persist($user);
    //     $entityManager->flush();

    //     return new Response ('the new user email and password is: '. $user->getEmail());

    // }
}