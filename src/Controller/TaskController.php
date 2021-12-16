<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tasks", name="task")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/list", name="_list")
     */
    public function list(TaskRepository $taskRepository): Response
    {
        $allTasks = $taskRepository->findAll();

        return $this->json($allTasks, Response::HTTP_OK, [], ['groups' => 'api_tasks']);
    }

    /**
     * @Route("/{id}", name="_read")
     */
    public function read($id, TaskRepository $taskRepository): Response
    {
        $currentTask = $taskRepository->find($id);

        return $this->json($currentTask, Response::HTTP_OK, [], ['groups' => 'api_tasks']);
    }

    /**
     * @Route("/add", name="_read", methods={"POST"})
     */
    public function add(Request $request, ManagerRegistry $managerRegistry): void
    {
        $newTask = new Task;
        $newTask->title = $request->get('title');
        $newTask->id = $request->get('id');
        $newTask->completion = $request->get('completion');
        $newTask->status = $request->get('status');

        $entityManager = $managerRegistry->getManager();
        $entityManager->persist($newTask);
        $entityManager->flush();
        
    }

        /**
     * @Route("/edit/{id}", name="_edit", methods={"PATCH", "PUT"})
     */
    public function edit($id, Request $request, ManagerRegistry $managerRegistry, TaskRepository $taskRepository): Response
    {
        
        $editTask = $taskRepository->find($id);
        $datas = $request->toArray();

        $editTask->setTitle($datas['title']);

        // $editTask->setId = $id;
        // $editTask->setCompletion = $datas['completion'];
        // $editTask->setStatus = $datas['status'];

        $entityManager = $managerRegistry->getManager();
        $entityManager->persist($editTask);
        $entityManager->flush();
        
        $response = new Response(
            'edit OK',
            Response::HTTP_NO_CONTENT,
            ['content-type' => 'text/html']
        );

        return $response;
    }
}
