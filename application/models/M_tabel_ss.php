<?php
 
class M_tabel_ss extends CI_Model {
 
 
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
 
    private function _get_datatables_query($column_search,$column_order,$order,$table,$table_join,$where)
    {
        


        $column_search = $column_search;
        $column_order = $column_order;
        $order = $order;
        $this->db->from($table);
        if ($table_join != null) {
            $this->db->join($table_join['table'],$table_join['join']);
        }
        if ($where != null) {
            $this->db->where($where);
        }
        
        
        $i = 0;
     

        // foreach ($this->column_search as $item) // looping awal
        foreach ($column_search as $item) // looping awal
        {
            if($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
            {
                 
                if($i===0) // looping awal
                {
                    $this->db->group_start(); 
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                // if(count($this->column_search) - 1 == $i) 
                if(count($column_search) - 1 == $i) 
                    $this->db->group_end(); 
            }
            $i++;
        }
         
        if(isset($_POST['order'])) 
        {
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            // $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
 
    function get_datatables($column_search,$column_order,$order,$table,$table_join,$where)
    {
        $this->_get_datatables_query($column_search,$column_order,$order,$table,$table_join,$where);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered($column_search,$column_order,$order,$table,$table_join,$where)
    {
        $this->_get_datatables_query($column_search,$column_order,$order,$table,$table_join,$where);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all($table,$table_join,$where)
    {
        
         
        $this->db->from($table);
        if ($table_join != null) {
            $this->db->join($table_join['table'],$table_join['join']);
        }
        if ($where != null) {
            $this->db->where($where);
        }
        
        
        
        return $this->db->count_all_results();
    }
 
}