<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
Use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/userlist", name="user_list")
     */
    public function userList()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();
        if (!$users) {
            throw $this->createNotFoundException(
                'No product found for id'
            );
        }
        dd($users);
        return new JsonResponse($users);

    }

    /**
     * @Route("/userlist/{id}", name="user_list_by_id")
     */
    public function getUserById($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        if (!$users) {
            throw $this->createNotFoundException(
                'No User found for this id'
            );
        }
        dd($users);
        return new JsonResponse($users);

    }

    /**
     * @Route("/delete/{id}", name="user_delete")
     */

    public function delete($id)
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
}
