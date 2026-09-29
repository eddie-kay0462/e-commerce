<?php

require_once __DIR__ . "/../core/db_class.php";

// Model for brands, categories and products.
// Only SQL lives here - no HTML, no $_POST, no $_SESSION, no redirects.
class ProductClass extends Database
{
    // Task 5: add a brand. Returns true/false.
    public function addBrand($name)
    {
        return $this->execute(
            "INSERT INTO brands (brand_name) VALUES (?)",
            [$name]
        );
    }

    // Task 5: every brand, A-Z. Returns an array of rows (empty if none).
    public function getAllBrands()
    {
        return $this->fetchAll("SELECT * FROM brands ORDER BY brand_name ASC");
    }

    // Task 6: one brand by id. Returns the row, or false if it doesn't exist.
    public function getBrandById($id)
    {
        return $this->fetchOne("SELECT * FROM brands WHERE brand_id = ?", [$id]);
    }

    // Task 6: rename a brand. Returns true/false.
    public function updateBrand($id, $name)
    {
        return $this->execute(
            "UPDATE brands SET brand_name = ? WHERE brand_id = ?",
            [$name, $id]
        );
    }
}
