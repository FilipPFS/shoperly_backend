<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['message' => 'Invalid JSON'], 400);
        }

        $requiredFields = ['category_id', 'name', 'description', 'price'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['message' => "Missing field: $field"], 400);
            }
        }

        $product = new Product();
        $product->setCategory($data['category_id']);
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);

        if (!isset($data['dateCreation'])) {
            $product->setDateCreation(new \DateTime());
        }

        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Product created successfully'], 201);
    }

    #[Route('/api/products/{id}', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['message' => 'Invalid JSON'], 400);
        }

        $requiredFields = ['category', 'name', 'description', 'price'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['message' => "Missing field: $field"], 400);
            }
        }

        $categoryId = $data['category']['id'];
        $category = $entityManager->getRepository(Category::class)->find($categoryId);

        if (!$category) {
            return new JsonResponse(['message' => 'Category not found'], 404);
        }

        $product->setCategory($category);
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);

        if (isset($data['dateCreation'])) {
            try {
                $product->setDateCreation(new \DateTime($data['dateCreation']));
            } catch (\Exception $e) {
                return new JsonResponse(['message' => 'Invalid date format for dateCreation'], 400);
            }
        }

        $entityManager->flush();

        return $this->json($product, 200, [], ['groups' => ['product:read']]);
    }


    #[Route('/api/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], 404);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Product deleted successfully'], 200);
    }
}
