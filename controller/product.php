<?php 
    class ProductController extends Controller{

        private $categoryModel;
        private $categoryLinkProductM;
        private $imageModel;
        private $fileRender = [
            'index' => 'product.index',
            'detail' => 'product.detail'
        ]; 

        function __construct(){
           $this->model = new ProductModel();
           $this->categoryModel = new CategoryModel();  //  Get All category
           $this->categoryLinkProductM= new CategoryLinkProductModel(); //  Get Category Link To Product
           $this->imageModel = new ImageModel();    //  Get Image Product
        }

        public function index(){
            if($this->model != NULL){
                $products = $this->model->getAllProduct();; //  get all from Product Model
                $products = mergeResult(['category_name', 'name'], ['category_list', 'image_list'], 'id', $products);
                foreach($products as $key => $product){   //  loop through products, edit some field
                    $description = $product['description'];
                    if (strlen($description) > 100) //  Short description
                        $products[$key]['description'] = substr($description, 0, 90) . '...';
                }
                $this->render($this->fileRender['index'],
                 [
                     'title' => 'Products',
                     'products' => $products
                 ]);
            }
        }

        public function detail(){
            if (!empty($_GET['q'])){    //  If query not emtpy
                $id = $_GET['q'];
                $result = $this->model->getSingleProduct($id); //  Query Product
                $result = mergeResult(['name'], ['image_list'], 'id', $result); // Merge to one 
                if(count($result) <= 0) //  If not have
                    return $this->renderNotFound();
                $title = $result[key($result)]['title'];    //  Get title product
                $this->render($this->fileRender['detail'],
                 [
                    'title' => $title,
                    'products' => $result
                 ]);
            }
            else    //  query is empty
                return $this->renderNotFound();
        }

    }
?>