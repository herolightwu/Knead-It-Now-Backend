<?php 

class User_model extends MY_Model {

    public function get_where($field, $value){
        $data = $this->get_all();
        $result = array();

        foreach($data as $d){
            if($d->$field==$value){
                $result[] = $d;
            }
        }

        return $result;
    }

    function fetch_for_id($id)
    {
        $query = $this->get_where('id', $id);
        if (count($query) == 0) {
            return null;
        }
        return $query[0];
    }

}