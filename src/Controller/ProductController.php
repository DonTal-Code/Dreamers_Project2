<?php

namespace App\Controller;

use App\Model\ProductManager;
use App\Model\CategoryManager;

class ProductController extends AbstractController
{

    public function indexUser()
    {
        $productManager = new ProductManager();
        $products = $productManager->selectAll();

        return $this->twig->render('Home/products.html.twig', ['products' => $products]);
    }
}
