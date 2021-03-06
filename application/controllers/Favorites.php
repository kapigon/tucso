<?php

class Favorites extends CI_Controller{    
    function __construct()
    {
        parent::__construct();
        $this->load->model('FavoriteModel');
        $this->load->model('TimesModel');
    }
    public function index($offset = 0){
        if(!$this->session->userdata('logged_in')){
            redirect('users/login');
        }
        
        // GET User ID from session
        $userId = $this->session->userdata('user_id');
        $this->db->where('UserId', $userId);
        
        // Pagination
        $config['base_url'] = base_url() . 'Favorites/index/';
        $config['total_rows'] = $this->db->count_all('Favorites');
        $config['per_page'] = 6;
        $config['uri_segment'] = 3;
        $config['attributes'] = array('class' => 'pagination-links');

        $this->pagination->initialize($config);
        $data['title'] = 'Danh sách đang thực hành';           

        $data['Buddhas'] = $this->FavoriteModel->get_Favorites($userId, $config['per_page'], $offset);

        $this->load->view('templates/header');
        $this->load->view('favorites/index', $data);
        $this->load->view('templates/footer');

    }
    public function addToFavorites($Buddha_Id = 0){
        if(!$this->session->userdata('logged_in')){
            redirect('users/login');
        }
        
        if($Buddha_Id > 0 && $this->session->userdata('user_id') > 0){
            
            $data = array(
                'UserId'    => $this->session->userdata('user_id'),
                'BuddhaId'  => $Buddha_Id,
                'Active'    => 1
            );
            
            if(!$this->FavoriteModel->isExitFavorites($this->session->userdata('user_id'), $Buddha_Id)){
                $this->FavoriteModel->add_Favorite($data);
                // Set message
                $this->session->set_flashdata('favorite_add', 'Đã thêm vào danh sách yêu thích thành công.!');
            }else{
                // Nếu tồn tại thì Active lên
                if($this->FavoriteModel->active($this->session->userdata('user_id'), $Buddha_Id))
                {
                    $this->session->set_flashdata('favorite_add', 'Đã thêm vào danh sách yêu thích thành công.!');
                }
                else{
                    // Set message
                    $this->session->set_flashdata('favorite_failed', 'Không thêm được, đã có trong danh sách.!');
                }
            }
            redirect('');
        }
    }

    public function delete($id){
        if(!$this->session->userdata('logged_in')){
            redirect('users/login');
        }

        if($this->session->userdata('user_id')){
            $this->FavoriteModel->delete_Favorite($id);
            redirect('favorites');
        }
    }
    
    public function nhaptucso($Buddha_Id){
        if(!$this->session->userdata('logged_in')){
            redirect('users/login');
        }
        
        //echo $Buddha_Id;
        //var_dump($Buddha_Id); die();
        
        // Check xem đã có có Times chưa, nếu có sẽ tạo Phiên mới
        if(!$this->TimesModel->isExitTimes($this->session->userdata('user_id'), $Buddha_Id)){
             $this->session->set_flashdata('times_add', 'Đã thêm vào danh sách yêu thích thành công.!');
        }else{
            $this->session->set_flashdata('times_failed', 'Không thêm được, đã có trong danh sách.!');
        }
        redirect('favorites');
    }
}