<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryController extends AbstractController
{
    #[Route('/api/categories', name: 'create_category', methods: ['POST'])]
    public function createCategory(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || empty($data['name'])) {
            return $this->json(['error' => 'Category name is required'], Response::HTTP_BAD_REQUEST);
        }

        $category = new Category();
        $category->setName($data['name']);

        $em->persist($category);
        $em->flush();

        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/categories/{id}', name: 'update_category', methods: ['PUT'])]
    public function updateCategory($id, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            return new JsonResponse(['message' => 'Category not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || empty($data['name'])) {
            return $this->json(['error' => 'Category name is required'], Response::HTTP_BAD_REQUEST);
        }

        $category->setName($data['name']);

        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            $validationErrors = [];
            foreach ($errors as $error) {
                $validationErrors[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $validationErrors], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();

        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/categories/{id}', name: 'delete_category', methods: ['DELETE'])]
    public function deleteCategory($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            return new JsonResponse(['message' => 'Category not found'], 404);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Category deleted successfully'], 200);
    }
}
