<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'about_intro' => 'I am a full-stack developer passionate about building modern and scalable applications.',
            'about_philosophy' => 'I believe in clean code, good architecture, and continuous learning.',
            'about_goals' => 'My goal is to create high-quality digital experiences and grow as a developer.',
            'projects' => [],   // or real projects
            'skills' => [],     // or real skills
        ]);
    }

}
