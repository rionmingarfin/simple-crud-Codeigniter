<?php defined('BASEPATH') OR exit ('no direct script acces allow');

class Product_model extends CI_Model {
    private $_tabels = 'product'; ///nama tabel kita
    //// nama kolom di tabel, harus sama huruf besar dan huruf kecilnya!
    public  $product_id;
    public  $name;
    public  $price;
    public  $image = 'default.jpg';
    public  $description;

    public function rules()
    {
        return [
            [
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'required'
            ],
            [
                'field' => 'price',
                'label' => 'Price',
                'rules' => 'numeric'
            ],
            [
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'required'
            ],
        ];
    }
    public function getAll()
    {
        //result=> fungsi untuk mengembalikan data dari hasil query
       return $this->db->get($this->_tabels)->result();
    }
    public function getById($id)
    {
        //"product_id"=> $id] == (=>)where  , row(funsi untuk mengambil data dari hasil query)
        return $this->db->get_where($this->_tabels,["product_id"=> $id])->row();
    }
    public function save()
    {
        $post = $this->input->post(); //ambil data dari form
        $this->product_id = uniqid();  //membuat id unik
        $this->name = $post['name'];  //isi field
        $this->price = $post['price']; //isi field
        $this->image = $this->_uploadImage();
        $this->description = $post['description']; //isi field
        $this->db->insert($this->_tabels,$this); // $this(dat yang akan disimpan)
    }
    public function update()
    {
        $post = $this->input->post();
        $this->product_id = $post['id'];
        $this->name = $post['name'];
        $this->price = $post['price'];
        //image
        if (!empty($_FILES["image"]["name"])) {
            $this->image = $this->_uploadImage();
        } else {
            $this->image = $post["old_image"];
        }
        $this->description = $post['description'];
        $this->db->update($this->_tabels,$this,array('product_id'=>$post['id']));
    }
    public function delete($id)
    {
        $this->_deleteImage($id);
       return $this->db->delete($this->_tabels,array('product_id'=>$id));
    }

    private function _uploadImage()
    {
        $config['upload_path']          = './upload/product/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['file_name']            = $this->product_id;
        $config['overwrite']			= true;
        $config['max_size']             = 1024; // 1MB
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('image')) {
            return $this->upload->data("file_name");
        }
        print_r($this->upload->display_errors());
        return "default.jpg";
    }
    private function _deleteImage($id)
    {
        $product = $this->getById($id);
        if ($product->image != "default.jpg") {
            $filename = explode(".", $product->image)[0];
            //Tanda bitang (*) setelah $filename artinya semua ektensi dipilih.
            return array_map('unlink', glob(FCPATH."upload/product/$filename.*"));
        }
    }

}



?>

