<?php

require_once __DIR__ . "/../classes/ProductClass.php";

// Middleman between actions/views and the ProductClass model.
class ProductController
{
    private $product;

    public function __construct()
    {
        $this->product = new ProductClass();
    }

    // Task 5
    public function addBrand($name)
    {
        return $this->product->addBrand($name);
    }

    // Task 5
    public function getAllBrands()
    {
        return $this->product->getAllBrands();
    }

    // Task 6
    public function getBrandById($id)
    {
        return $this->product->getBrandById($id);
    }

    // Task 6
    public function updateBrand($id, $name)
    {
        return $this->product->updateBrand($id, $name);
    }

    public function addCategory($name)
    {
        return $this->product->addCategory($name);
    }

    public function getAllCategories()
    {
        return $this->product->getAllCategories();
    }

    public function getCategoryById($id)
    {
        return $this->product->getCategoryById($id);
    }

    public function updateCategory($id, $name)
    {
        return $this->product->updateCategory($id, $name);
    }
}
