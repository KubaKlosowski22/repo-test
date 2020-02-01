<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
Use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class MainController extends AbstractController

{

    /**
     * @Route("/", name="main_page")
     */
    public function index()
    {
        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/start", name="logged_page")
     */
    public function logged()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('main/loggedmain.html.twig');
    }

    /**
     * @Route("/userslist", name="user_list")
     * Method({"POST"})
     */

    public function usersList()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers,$encoders);

        //$this->denyAccessUnlessGranted('ROLE_USER');
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();
        if (!$users) {
            throw $this->createNotFoundException(
                'No product found for id'
            );
        }
        $jsonData = $serializer->serialize($users, 'json');
        dd($jsonData);
        return new JsonResponse($jsonData);
    }

    /**
     * @Route("/userlist/{id}", name="user_list_by_id")
     * Method({"POST"})
     */
    public function getUserById($id)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers,$encoders);

        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'No User found for this id'
            );
        }
        $jsonData = $serializer->Serialize($user,'json');


        dd($jsonData);
        return new JsonResponse($jsonData);

    }

    /**
     * @Route("/delete/{id}", name="user_delete")
     */

    public function deleteUser($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $currentUser = $this->getUser();

        if ($user != $currentUser) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        } else {
            echo "You cant delete this User";
        }

    }
    /**
     * @Route("/emailupdate/{id}", name="email_update")
     */

    public function updateEmail($id){
        $request = Request::createFromGlobals();
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager= $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        $mail= $request->get("email");

        $user->setEmail($mail);

        $entityManager->flush();
    }

    /**
     * @Route("/passupdate/{id}", name="pass_update")
     *
     */

    public function updatePassword(UserPasswordEncoderInterface $encoder, $id){
        $request = Request::createFromGlobals();
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager= $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);


        $pass= $request->get("pass");
        $encoded = $encoder->encodePassword($user,$pass);
        $user->setPassword($encoded);
        $entityManager->flush();
    }
}
