<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use DateTime;
use DateTimeImmutable;
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
    public function add(Request $request, ManagerRegistry $managerRegistry, CategoryRepository $categoryRepository): Response
    {
        $newTask = new Task;
        $datas = $request->toArray();

        $newTask->setTitle($datas['title']);
        $newTask->setCompletion(0);
        $newTask->setStatus(1);
        $newTask->setCreatedAt(new DateTimeImmutable());

        $selectedCategory = $categoryRepository->find($datas['categoryId']);

        $newTask->setCategory($selectedCategory);

        $entityManager = $managerRegistry->getManager();
        $entityManager->persist($newTask);
        $entityManager->flush();

        $newTaskTitle = json_encode($datas['title']);

        $response = new Response(
            $newTaskTitle,
            Response::HTTP_CREATED,
            ['content-type' => 'text/html']
        );  

        return $response;
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
