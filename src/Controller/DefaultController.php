<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", methods="GET", name="homepage")
     */
    public function index(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $productList = $entityManager->getRepository(Product::class)->findAll();
//        dd($productList);

        return $this->render('main/default/index.html.twig', []);
    }
//
//    /**
//     * @Route("/product-add", methods="GET", name="product_add")
//     */
//    public function productAdd(Request $request): Response
//    {
//        $product = new Product();
//        $product->setTitle('Product '.rand(1, 100));
//        $product->setDescription('smth');
//        $product->setPrice(10);
//        $product->setQuantity(1);
//
//        $entityManager = $this->getDoctrine()->getManager();
//        $entityManager->persist($product);
//        $entityManager->flush();
//
//        return $this->redirectToRoute('homepage');
//    }

    /**
     * @Route("/edit-product/{id}", methods="GET|POST", name="product_edit", requirements={"id"="\d+"})
     * @Route("/add-product", methods="GET|POST", name="product_add")
     */
    public function editProduct(Request $request, int $id = null): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($id) {
            $product = $entityManager->getRepository(Product::class)->find($id);
        } else {
            $product = new Product();
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush($product);
            return $this->redirectToRoute("product_edit", ['id' => $product->getId()]);
        }
        return $this->render('main/default/edit_product.html.twig', ['form' => $form->createView()]);
    }
}
