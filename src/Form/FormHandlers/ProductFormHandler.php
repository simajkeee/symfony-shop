<?php

namespace App\Form\FormHandlers;

use App\Entity\Product;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;

class ProductFormHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(EntityManagerInterface $entityManager, FileUploader $fileUploader)
    {
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @param Product $product
     * @param Form    $form
     * @return Product
     */
    public function handle(Product $product, Form $form): Product
    {
        $fileToUpload = $form->get('newImage')->getData();
        $uploadResult = $this->fileUploader->upload($fileToUpload);
        dd($uploadResult);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
        return $product;
    }
}