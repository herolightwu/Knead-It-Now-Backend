<?php 

class Message_post_model extends MY_Model {

    //protected $order_by = array('id', 'DESC');

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

    function delete_for_bookid($bookid){
        $sql = "DELETE FROM message_posts WHERE ";

        $sql .= "book_id = '$bookid'";
        $this->db->query($sql);

        return ;
    }

    public function setReadMessages($bookid, $rv_id){
        $sql = "UPDATE message_posts SET read_status = '1' WHERE ";

        $sql .= "book_id = '$bookid' AND receiver_id = '$rv_id'";
        $this->db->query($sql);

        return ;
    }
}