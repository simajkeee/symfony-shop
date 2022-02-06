<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\FormHandlers\ProductFormHandler;
use App\Form\ProductType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function add(
        Request $request,
        ProductFormHandler $productFormHandler,
        Product $product = null
    ): Response {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $productFormHandler->handle($product, $form);
            $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
        }
        return $this->render('admin/product/edit.html.twig', [
            'form'    => $form->createView(),
            'product' => $product,
        ]);
    }
}
