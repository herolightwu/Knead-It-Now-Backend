<?php 

class Event_post_model extends MY_Model {

    protected $order_by = array('id', 'DESC');

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

    public function delete_for_bookid($bookid){
        $sql = "DELETE FROM event_posts WHERE ";

        $sql .= "book_id = '$bookid'";
        $this->db->query($sql);

        return ;
    }

    public function getUnreadEvents($userid){
        $sql = "SELECT * FROM event_posts WHERE ";

        $sql .= "user_id = '$userid' AND isread = '0'";
        $data = $this->db->query($sql)->result();

        return $data;
    }

    public function setReadRequestEvent($bookid){
        $sql = "UPDATE event_posts SET isread = '1' WHERE ";

        $sql .= "book_id = '$bookid' AND type_id = '11'";
        $this->db->query($sql);

        return ;
    }

    public function setReadFinishEvent($bookid){
        $sql = "UPDATE event_posts SET isread = '1' WHERE ";

        $sql .= "book_id = '$bookid' AND type_id = '2'";
        $this->db->query($sql);

        return ;
    }

    public function setReadSellerFinishEvent($bookid){
        $sql = "UPDATE event_posts SET isread = '1' WHERE ";

        $sql .= "book_id = '$bookid' AND type_id = '12'";
        $this->db->query($sql);

        return ;
    }

    public function setReadMessageEvent($bookid){
        $sql = "UPDATE event_posts SET isread = '1' WHERE ";

        $sql .= "book_id = '$bookid' AND (type_id = '4' OR type_id = '14')";
        $this->db->query($sql);

        return ;
    }
}