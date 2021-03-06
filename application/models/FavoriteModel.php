<?php
    class FavoriteModel extends CI_Model{

        public function __construct(){
            $this->load->database();
        }

        public function get_Favorites($user_Id = 0, $limit = FALSE, $offset = FALSE){
            if($limit){
                $this->db->limit($limit, $offset);
            }
            
            //$query = $this->db->get_where('Favorites', array('UserId' => $user_Id));
            
            $query = $this->db->select('bud.ID, bud.Name, bud.Slug, bud.HinhAnh, '
                    . 'bud.ChungTu, bud.ChanNgonTiengPhan, bud.ChanNgonTiengViet,'
                    . 'bud.GhiChu,'
                    . 'fav.ID as favID')
            ->from('Favorites as fav')
            ->where(array('fav.UserId' => $user_Id, 'fav.Active' => 1))
            //->where('fav.UserId', $user_Id)
            ->join('Buddhas as bud', 'fav.BuddhaID = bud.ID')
            ->get();
            
            return $query->result_array();
        }

        public function isExitFavorites($user_Id, $Buddha_Id){
            $query = $this->db->get_where('Favorites', array('UserId' => $user_Id, 'BuddhaId' =>$Buddha_Id));
            if(!empty($query->row_array())) {
                /*$query = $this->db->get_where('Favorites', array('UserId' => $user_Id, 'BuddhaId' =>$Buddha_Id, 'Active' => 0));
                if(!empty($query->row_array())) {
                    return true;
                }*/
                return true;
            }else{
                return false;
            }
        }
        
        public function add_Favorite($data){
            return $this->db->insert('Favorites', $data);
        }
        
        public function active($user_Id, $Buddha_Id){
            $query = $this->db->get_where('Favorites', array('UserId' => $user_Id, 'BuddhaId' =>$Buddha_Id));
            $data = $query->row_array();
            
            $data['Active'] = 1;
            $this->db->where('ID', $data['ID']);
            
            return $this->db->update('Favorites', $data);
        }
        
        public function delete_Favorite($Id){
            $data['Active'] = 0;
            
            $this->db->where('ID', $Id);
            return $this->db->update('Favorites', $data);
        }
    }