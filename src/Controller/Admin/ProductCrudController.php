<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/product", name="admin_product_")
 */
class ProductCrudController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function index(): Response
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findBy(['isDeleted' => false], ['id' => 'DESC'], 50);
        return $this->render('admin/product/list.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @Route("/add", name="add")
     */
    public function add(int $id = null): Response
    {
        return new Response('add/edit product page');
    }
}
