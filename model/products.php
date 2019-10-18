<?php 
    class ProductModel extends database{

        function __construct(){
            $this->connect('products');
        }

        public function getAllProduct(){
            if(is_null($this->pdo))
                return NULL;
            $stmt = $this->pdo->prepare("SELECT products.*, images.name, images.product_id, cp.category_name FROM products 
                                        LEFT JOIN categorys_link_products cp 
                                        ON products.id = cp.product_id
                                        LEFT JOIN images
                                        on products.id = images.product_id
                                        ");
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        }

        public function getSingleProduct($id){
            try{
            if(is_null($this->pdo))
                return NULL;
            $stmt = $this->pdo->prepare("SELECT products.*, images.name
                                 FROM products 
                                 INNER JOIN images ON images.product_id = products.id
                                 WHERE products.id = $id");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            catch(PDOException $err){
                return [];
            }
        }

    }
?>