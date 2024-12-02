<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
}
