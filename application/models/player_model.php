<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Player_model extends CI_Model
{

    public $table = 'pemain';
    public $id = 'id_data_pemain';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // get all
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }


    function get_all_limit_page($limit,$offset, $page, $key, $s){
            if ($limit!=-1 and $offset!=-1) {
                $this->db->limit($limit,$offset);
            }

            $mapping = [
                'name' => 'nama_data_pendaftaran'
            ];
            if (isset($mapping[$key])) {
                $this->db->where($mapping[$key].' like "%'.$s.'%" ');
            }


           $return = $this->db->select('id_data_pemain AS id ,
            d.nama_data_pendaftaran AS name, 
            d.posisi_data_pendaftaran AS position,   namanopunggung_data_pemain AS number,  
            warganegara_data_pendaftaran AS nationality , 
            photo_data_pendaftaran AS photo_url')
                            ->from('pemain p')
                            ->join('pendaftaran d','p.id_data_pendaftaran = d.id_data_pendaftaran')
                            ->order_by('d.nama_data_pendaftaran', 'asc')
                            ->get()
                            ->result();     
                       
            return [
                'data' => $return,
                'total_page' => $offset!=-1 ? ceil($this->total_rows($key, $s)/$limit) : 1,
                'current'   => $offset!=-1 ? $page : 1 ,
            ];
    }



    // get data by id
    function get_by_id($id)
    {
        $query = $this->db->query("SELECT id_data_pemain AS id , d.nama_data_pendaftaran AS name, d.posisi_data_pendaftaran AS position,   
        namanopunggung_data_pemain AS number,  warganegara_data_pendaftaran AS nationality , photo_data_pendaftaran AS photo_url,
        d.nama_data_pendaftaran AS description, tempatlahir_data_pendaftaran AS birth_place, tinggiberatbadan_pemain, clubsaatini_pemain as club,
        tanggallahir_data_pendaftaran as birth_date, '-' AS debut_date
        FROM pemain p
        left JOIN pendaftaran d ON (p.id_data_pendaftaran = d.id_data_pendaftaran) where id_data_pemain = ".$id)->result_array();

        $query_careers =  $this->db->query("select * from pemain_riwayat where id_pemain = ".$id." order by tahun desc")->result_array();

        $return = array();

        $appear = 0;
        $goal = 0;
        $career = array();
        foreach ($query_careers as $k) {
            $career[] = [
                "club_name" => $k['klub'],
                "start_date" => '-',
                "end_date" => $k['tahun'] ,
                "appearance" =>  $k['main'],
                "goal" => $k['gol'],
            ];
            $appear += $k['main'];
            $goal += $k['gol'];
        }

        foreach ($query as $q) {
            $wh = explode('/', $q['tinggiberatbadan_pemain']);
            $height = $wh[0];
            $weight = isset($wh[1])?$wh[1]:"";
            $birth_date = explode( '/',$q['birth_date']);
            if (isset($birth_date[2])) {
                 $birth_date = $birth_date[2].'-'.$birth_date[1].'-'.$birth_date[0];
            }else{
                $birth_date='-';
            }
           
           
            $return  = [
                "id" =>  $q['id'],
                "name" => $q['name'],
                "position" => $q['position'],
                "number" => $q['number'],
                "nationality" => $q['nationality'],
                "photo_url" => $q['photo_url'],
                "description" => $q['description'],
                "birth_place" => $q['birth_place'],
                "height" => $height,
                "weight" => $weight,
                "total_appearance" => $appear,
                "total_goal" => $goal,
                "club" =>$q['club'],
                "birth_date" => $q['birth_date'],
                "debut_date" => '-',
            ];
        }

       

       
 
        $return['careers'] = $career;

        return $return;

    }
    
    // get total rows
    function total_rows($key,$s) {
        $mapping = [
                'name' => 'nama_data_pendaftaran'
        ];
        if (isset($mapping[$key])) {
                $this->db->where($mapping[$key].' like "%'.$s.'%" ');
        }
        return $this->db->select('id_data_pemain AS id ,
            d.nama_data_pendaftaran AS name, 
            d.posisi_data_pendaftaran AS position,   namanopunggung_data_pemain AS number,  
            warganegara_data_pendaftaran AS nationality , 
            photo_data_pendaftaran AS photo_url')
                            ->from('pemain p')
                            ->join('pendaftaran d','p.id_data_pendaftaran = d.id_data_pendaftaran')
                            ->order_by('d.nama_data_pendaftaran', 'asc')
                      ->count_all_results();
    }

    // get data with limit
    function index_limit($limit, $start = 0) {
        $this->db->order_by($this->id, $this->order);
        $this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }
    
    // get search total rows
    function search_total_rows($keyword = NULL) {
    $this->db->like('jenisname', $keyword);
    $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get search data with limit
    function search_index_limit($limit, $start = 0, $keyword = NULL) {
        $this->db->order_by($this->id, $this->order);
    $this->db->or_like('jenisname', $keyword);
    $this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    // update data
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
    }

    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }

  

    

}

/* End of file jenis_model.php */
/* Location: ./application/models/kecamatan_model.php */