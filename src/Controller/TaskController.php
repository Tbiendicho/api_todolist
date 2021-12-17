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

        $newTaskId = $newTask->getId();

        $response = new Response(
            $newTaskId,
            Response::HTTP_CREATED,
            ['content-type' => 'text/html']
        );

        return $response;
    }

    /**
     * @Route("/edit/{id}", name="_edit", methods={"PATCH", "PUT"})
     */
    public function edit($id, Request $request, ManagerRegistry $managerRegistry, TaskRepository $taskRepository, CategoryRepository $categoryRepository): Response
    {
        $editTask = $taskRepository->find($id);
        $datas = $request->toArray();
        
        if (isset($datas['title'])) {
            $editTask->setTitle($datas['title']);
        } elseif (isset($datas['completion'])) {
            $editTask->setCompletion($datas['completion']);
        } elseif (isset($datas['status'])) {
            $editTask->setStatus($datas['status']);
        } elseif (isset($datas['category'])) {
            $selectedCategory = $categoryRepository->find($datas['category']);
            $editTask->setCategory($selectedCategory);
        }

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

    /**
     * @Route("/delete/{id}", name="_delete", methods={"DELETE"})
     */
    public function delete($id, ManagerRegistry $managerRegistry, TaskRepository $taskRepository): Response
    {

        $taskToDelete = $taskRepository->find($id);
        $entityManager = $managerRegistry->getManager();
        $entityManager->remove($taskToDelete);
        $entityManager->flush();

        $response = new Response(
            'delete OK',
            Response::HTTP_NO_CONTENT,
            ['content-type' => 'text/html']
        );

        return $response;
    }
}
