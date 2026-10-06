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

    // Add a category. Returns true/false.
    public function addCategory($name)
    {
        return $this->execute(
            "INSERT INTO categories (cat_name) VALUES (?)",
            [$name]
        );
    }

    // Every category, A-Z. Returns an array of rows (empty if none).
    public function getAllCategories()
    {
        return $this->fetchAll("SELECT * FROM categories ORDER BY cat_name ASC");
    }

    // One category by id. Returns the row, or false if it doesn't exist.
    public function getCategoryById($id)
    {
        return $this->fetchOne("SELECT * FROM categories WHERE cat_id = ?", [$id]);
    }

    // Rename a category. Returns true/false.
    public function updateCategory($id, $name)
    {
        return $this->execute(
            "UPDATE categories SET cat_name = ? WHERE cat_id = ?",
            [$name, $id]
        );
    }

    // ---------------- Products ----------------

    // Every query below JOINs categories and brands, so each product row
    // also carries cat_name and brand_name (the view can show "Phones"
    // instead of "3"). LEFT JOIN keeps a product even if its category or
    // brand row is missing (there are no foreign keys on the server).
    private $productSelect = "SELECT p.*, c.cat_name, b.brand_name
        FROM products p
        LEFT JOIN categories c ON c.cat_id = p.product_cat
        LEFT JOIN brands b ON b.brand_id = p.product_brand";

    // Add a product. Returns true/false.
    public function addProduct($cat, $brand, $title, $price, $desc, $image, $keywords)
    {
        return $this->execute(
            "INSERT INTO products (product_cat, product_brand, product_title, product_price,
                                   product_desc, product_image, product_keywords)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$cat, $brand, $title, $price, $desc, $image, $keywords]
        );
    }

    // Edit a product. Returns true/false.
    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords)
    {
        return $this->execute(
            "UPDATE products SET product_cat = ?, product_brand = ?, product_title = ?,
                    product_price = ?, product_desc = ?, product_image = ?, product_keywords = ?
             WHERE product_id = ?",
            [$cat, $brand, $title, $price, $desc, $image, $keywords, $id]
        );
    }

    // Every product, newest first. Returns an array of rows (empty if none).
    public function getAllProducts()
    {
        return $this->fetchAll($this->productSelect . " ORDER BY p.product_id DESC");
    }

    // One product by id. Returns the row, or false if it doesn't exist.
    public function getProductById($id)
    {
        return $this->fetchOne($this->productSelect . " WHERE p.product_id = ?", [$id]);
    }

    // Search and filter products. Every argument is optional:
    //   $keyword  - matched against the title and the keywords column
    //   $cat_id   - only products in this category
    //   $brand_id - only products of this brand
    // The WHERE clause is built from the filters that were given, but the
    // values still go in as "?" placeholders, so this is safe from SQL injection.
    public function searchProducts($keyword = '', $cat_id = null, $brand_id = null)
    {
        $where  = [];
        $params = [];

        if ($keyword !== '') {
            // % and _ are wildcards in LIKE - escape them so a search for
            // "100%" looks for a real % sign
            $like = '%' . addcslashes($keyword, '%_\\') . '%';
            $where[]  = "(p.product_title LIKE ? OR p.product_keywords LIKE ?)";
            $params[] = $like;
            $params[] = $like;
        }
        if ($cat_id) {
            $where[]  = "p.product_cat = ?";
            $params[] = $cat_id;
        }
        if ($brand_id) {
            $where[]  = "p.product_brand = ?";
            $params[] = $brand_id;
        }

        $sql = $this->productSelect;
        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY p.product_id DESC";

        return $this->fetchAll($sql, $params);
    }
}
