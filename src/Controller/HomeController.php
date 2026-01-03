<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Skill;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    #[Route('/', name:  'app_index')]
    public function index(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {

        $isLoggedIn = $session->has('user_id');
        $userName = $isLoggedIn ? $session->get('user_name') : null;
        $userEmail = $isLoggedIn ? $session->get('user_email') : null;


        $projects = $entityManager->getRepository(Project::class)
            ->findBy([], ['id' => 'DESC'], 6);


        $allSkills = $entityManager->getRepository(Skill:: class)
            ->findAll();


        $skills = [];
        foreach ($allSkills as $skill) {
            $category = $skill->getCategory();
            if (!isset($skills[$category])) {
                $skills[$category] = [];
            }
            $skills[$category][] = $skill;
        }

        return $this->render('home/index.html.twig', [
            'is_logged_in' => $isLoggedIn,
            'user_name' => $userName,
            'user_email' => $userEmail,
            'projects' => $projects,
            'skills' => $skills,
        ]);
    }
}
