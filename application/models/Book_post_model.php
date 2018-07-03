<?php 

class Book_post_model extends MY_Model {

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

    public function get_today_books($uid, $today){
        $sql = "SELECT * FROM book_posts WHERE ";

        $sql .= "seller_id = '$uid' AND start_date = '$today'";
        $data = $this->db->query($sql)->result();

        return $data;
    }

    public function matchBooks($sdate, $duration){
        $sql = "SELECT * FROM book_posts WHERE ";

        $sql .= "start_date = '$sdate' AND duration = '$duration' AND status = 'posted'";
        $data = $this->db->query($sql)->result();

        return $data;
    }

    public function get_confirmed_books(){
        $sql = "SELECT * FROM book_posts WHERE ";

        $sql .= "status = 'finished' ORDER BY book_time DESC";
        $data = $this->db->query($sql)->result();

        return $data;
    }

}