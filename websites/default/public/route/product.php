<?php
    $productController = new ProductController();
    

    route('/product/detail?{q}', function(){
        global $productController;
        $productController->detail();
    });
    
    route('/product', function(){
        global $productController;
        $productController->index();
    });
    
?>