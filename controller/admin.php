<?php 
    class AdminController extends Controller{

        private $prodModel;
        private $pathUpLoad = PATH_IMAGE_UPLOAD. '/';

        function __construct(){
            $this->prodModel = new ProductModel();
            $this->fileRender = [
                'add-product' => 'admin.add-product'
            ];
        }

        public function addProduct(){
            $this->render($this->fileRender['add-product'],
            [
                'title' => 'Add Product'
            ]);
            return;
        }

        public function upload(){
            //  Check if any field is empty
            if(checkEmpty([$_POST['title'], $_POST['description'], $_POST['price'], $_POST['status'], $_POST['rate']]))
            {
                setHTTPCode(500, 'Error while upload file');
                return;
            }

            if($this->prodModel->checkProduct('title', $_POST['title'])){   //  Check title if exist
                setHTTPCode(500, 'Product exist!');
                return;
            }

            $uploadHeader = $this->uploadHeader();  //  upload header image to storage, return name header image
            if(!$uploadHeader){ //  Check if error remove header image from storage
                removeFiles([$uploadHeader], PATH_IMAGE_UPLOAD);
                setHTTPCode(500, 'Error while upload file');
                return;
            }

            //  Create product to database, return the id of product when create finish
            $idNewProduct = $this->prodModel->addNewProduct(
                                                $_POST['title'], 
                                                $_POST['description'], 
                                                (float)$_POST['price'],
                                                 $uploadHeader, 
                                                 $_POST['status'], 
                                                 $_POST['rate']);
            if(!$idNewProduct && $idNewProduct !== 0){ // check if fail remove header image from storage
                removeFiles([$uploadHeader], PATH_IMAGE_UPLOAD);
                setHTTPCode(500, 'Error while create new Product');
                return;
            }

            //  upload thumbnail to storage , return list name image;
            $uploadThumbnail = $this->uploadThumbnail();    
            if(!$uploadThumbnail){  // if error
                setHTTPCode(500, 'Error while upload file');
                return;
            }
            //  create image thumbnail to database
            $queryAddThumbnail = []; //  query to add many thumbnail image
            foreach($uploadThumbnail as $image){    //  Loop array of storage image name return from above
                $queryAddThumbnail[] = "(DEFAULT, " . $idNewProduct . ", 'thumbnail', '". $image . "', DEFAULT)"; 
            }
            $queryAddThumbnail = implode(',', $queryAddThumbnail);
            $result = $this->prodModel->addThumnailProduct($queryAddThumbnail); //  Create thumbnails to db
            if(!$result){   //  if fail delete thumbnail in storage
                removeFiles($uploadThumbnail, PATH_IMAGE_UPLOAD);
                setHTTPCode(500, "Error while save thumbnail");
                return;
            }
            setHTTPCode(200, "Create successful!");
        }

        public function uploadHeader(){
            if (empty($_FILES['header'])) 
                return false;

             if ($_FILES['header']['error'] != UPLOAD_ERR_OK || !getimagesize($_FILES['header']['tmp_name']))
                 return false;
             
             $newName = $this->createNameImg($_FILES['header']['name']);
             move_uploaded_file($_FILES['header']['tmp_name'], $this->pathUpLoad . $newName);
             return $newName;
        }

        public function uploadThumbnail(){
            if(!empty($_FILES['thumbnail'])){
                $total = count($_FILES['thumbnail']['name']);
                $listImage = [];
                for($i = 0; $i < $total; $i++){
                    if ($_FILES['thumbnail']['error'][$i] != UPLOAD_ERR_OK || !getimagesize($_FILES['thumbnail']['tmp_name'][$i]))
                        return false;

                    $newName = $this->createNameImg($_FILES['thumbnail']['name'][$i]);
                    move_uploaded_file($_FILES['thumbnail']['tmp_name'][$i], $this->pathUpLoad . $newName);
                    $listImage[] = $newName;
                }
                return $listImage;
            }
                return false;
            }

        
        
            public function createNameImg($file){
            $newName = createRanDomString() .  '.' . getExtFile($file);
            while (file_exists($this->pathUpLoad . $newName)){
                $newName = createRanDomString() . '.' . getExtFile($file);
            }
            return $newName;
        }
    }
    
?>