<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }
  
    #[Route('/new/{projectId}', name: 'task_new', methods: ['GET', 'POST'])]

    public function new(Request $request, ProjectRepository $ProjectRepository, int $projectId): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $project =$ProjectRepository->find($projectId);
            $task->setDateCreation(new \DateTime());
            $task->setProjet($project);
            $task->setStatus('En cours');
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $projectId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/deleteTask', name: 'task_delete', methods: ['GET','POST'])]


    public function delete(int $id=1, TaskRepository $TaskRepository): Response
    {
        
            $Task = $TaskRepository->find($id);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($Task);
            $entityManager->flush();
       
            return $this->redirectToRoute('project_show', ['id' => $Task->getProjet()->getId()], Response::HTTP_SEE_OTHER);
            
    }


    #[Route('/{id}/updateStatus', name: 'status_update', methods: ['GET','POST'])]
    public function statusUpdate(int $id=1, TaskRepository $TaskRepository): Response
    {
        
            $Task = $TaskRepository->find($id);
            $Task->setStatus("Terminé");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Task);
            $entityManager->flush();
       
        return $this->redirectToRoute('project_show', ['id' => $Task->getProjet()->getId()], Response::HTTP_SEE_OTHER);
            
    }
}
