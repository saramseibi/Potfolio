<?php

namespace App\Controller\Admin;

use App\Entity\Skill;
use App\Form\SkillType;
use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectType;
use App\Repository\SkillRepository;
use App\Repository\ProjectRepository;
use App\Repository\MessageRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(
        ProjectRepository $projectRepository,
        SkillRepository $skillRepository,
        MessageRepository $messageRepository
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'projectsCount' => $projectRepository->count([]),
            'skillsCount' => $skillRepository->count([]),
            'messagesCount' => $messageRepository->count([]),
        ]);
    }
    #[Route('/admin/project/new', name: 'admin_project_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $slug = $slugger->slug($project->getTitle())->lower();
                $dir = $this->getParameter('projects_images_directory') . '/' . $slug;

                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                $filename = 'cover.' . $imageFile->guessExtension();
                $imageFile->move($dir, $filename);

                $project->setImage($slug . '/' . $filename);
            }

            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/project.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/skill/new', name: 'admin_skill_new')]
    public function newSkill(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $skill = new Skill();
        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($skill);
            $em->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/skill.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/goproject', name: 'admin_projects')]
    public function manageProjects(
        ProjectRepository $projectRepo,
        SkillRepository $skillRepo
    ): Response {

        $projects = $projectRepo->findAll();


        $skills = $skillRepo->findAll();


        $skillsByCategory = [];

        foreach ($skills as $skill) {
            $category = $skill->getCategory();

            if (!isset($skillsByCategory[$category])) {
                $skillsByCategory[$category] = [];
            }

            $skillsByCategory[$category][] = $skill;
        }

        return $this->render('admin/goproject.html.twig', [
            'projects' => $projects,
            'skillsByCategory' => $skillsByCategory,
        ]);
    }

    #[Route('/admin/project/{id}', name: 'admin_project_delete', methods: ['POST'])]
    public function deleteProject(Request $request, Project $project, EntityManagerInterface $em)
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $em->remove($project);
            $em->flush();
        }

        return $this->redirectToRoute('admin_projects');
    }
    #[Route('/admin/skill/{id}', name: 'admin_skill_delete', methods: ['POST'])]
    public function deleteSkill(Request $request, Skill $skill, EntityManagerInterface $em)
    {
        if ($this->isCsrfTokenValid('delete'.$skill->getId(), $request->request->get('_token'))) {
            $em->remove($skill);
            $em->flush();
        }

        return $this->redirectToRoute('admin_projects');
    }
    #[Route('/admin/messages', name: 'admin_messages')]
    public function showMessages(MessageRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/messages.html.twig', [
            'messages' => $repo->findBy([], ['createdAt' => 'DESC'])
        ]);
    }



}
