<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * product Controller with Swagger annotations
 * Reference: https://github.com/zircote/swagger-php/
 */
class Book extends API_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Book_post_model', 'books');
        $this->load->model('User_model', 'users');
        $this->load->model('Business_info_model', 'business_infos');
        $this->load->model('Event_post_model', 'events');
        $this->load->model('Message_post_model', 'messages');
        $this->load->model('Massage_type_model', 'massages');
        $this->load->model('Review_post_model', 'reviews');
        $this->load->model('Payment_post_model', 'payments');
    }

    public function index_get()
    {
        $data = $this->books
            ->select('id, seller_id, start_date, start_time, duration, cost, seller_note, status, buyer_id, massage_type, buyer_note, book_time')
            ->get_all();
        $this->response($data);
    }

    public function id_get($id)
    {
        $data = $this->books
            ->select('id, seller_id, start_date, start_time, duration, cost, seller_note, status, buyer_id, massage_type, buyer_note, book_time')
            ->get($id);

        $this->response($data);
    }

    public function index_post()
    {
        $data = $this->books
            ->select('id, seller_id, start_date, start_time, duration, cost, seller_note, status, buyer_id, massage_type, buyer_note, book_time')
            ->get_all();

        foreach ($data as $d) {
            $user = $this->user->where(array('id'=>$d->user_id))->get_all();
            $d->user = $user[0];
        }
        $this->response($data);
    }

    public function postBook_post(){
        $userId = $this->post('uid');
        $sDate = $this->post('start_date');
        $sTime = $this->post('start_time');
        $duration = $this->post('duration');
        $cost = $this->post('cost');
        $auto_confirm = $this->post('auto_confirm');
        $seller_note = $this->post('note');

        $inputdata = array(
            'seller_id' => $userId,
            'start_date' => $sDate,
            'start_time' =>$sTime,
            'duration' =>$duration,
            'cost' =>$cost,
            'auto_confirm' => $auto_confirm,
            'seller_note' => $seller_note,
            'status' => 'posted',
        );

        $bookId= $this->books->insert($inputdata);
        if($bookId){
            $result = array(
                'status' => 'success',
                'data' => $bookId,
            );
            $this->response($result);
        } else{
            $result = array(
                "status" => "failed",
                'error' => 'Availability setting was failed.',
            );
            $this->response($result);
        }

    }

    public function getAvailability_post(){
        $seller_id = $this->post('uid');
        $todate = $this->post('today');
        $data = $this->books->get_today_books($seller_id, $todate);
        $result = array(
            'status' => 'success',
            'data' => $data,
        );
        $this->response($result);
    }

    public function searchBooks_post(){
        $massage_type = $this->post('massage_type');
        $start_date = $this->post('start_date');
        $duration = $this->post('duration');
        $gender = $this->post('gender');
        $data = $this->books->matchBooks($start_date, $duration);
        $matchlist = array();
        foreach($data as $one){
            $seller = $this->users->fetch_for_id($one->seller_id);
            if($gender == 'Either' || $gender == $seller->gender){
                $bus_data = $this->business_infos->get_where('user_id',$seller->id);
                $types = explode(",", $bus_data[0]->massage_types);
                foreach($types as $d){
                    if($d == $massage_type){
                        $one->bs_address = $seller->address;
                        $one->bs_location = $seller->location;
                        $one->bs_name = $bus_data[0]->name;
                        $one->seller_rate = $seller->rate;
                        $one->seller_photo = $seller->photo;
                        $one->seller_name = $seller->first_name.' '.$seller->last_name;
                        $one->massage_type = $massage_type;
                        $matchlist[]=$one;
                        break;
                    }
                }
            }
        }
        $result = array(
            'status' => 'success',
            'data' => $matchlist,
        );
        $this->response($result);
    }

    public function requestAppointment_post(){
        $bookid = $this->post('book_id');
        $buyerid = $this->post('buyer_id');
        $type = $this->post('massage_type');
        $bnote = $this->post('note');
        $book_time = $this->post('book_time');

        $this->books->update_field($bookid, 'buyer_id', $buyerid);
        $this->books->update_field($bookid, 'massage_type', $type);
        $this->books->update_field($bookid, 'buyer_note', $bnote);
        $this->books->update_field($bookid, 'book_time', $book_time);
        $this->books->update_field($bookid, 'status', 'requested');
        $bdata = $this->books->fetch_for_id($bookid);
        $seller = $this->users->fetch_for_id($bdata->seller_id);
        $buyer = $this->users->fetch_for_id($buyerid);
        $mas_type = $this->massages->fetch_for_id($type);

        if($bdata->auto_confirm == '1'){
            $this->books->update_field($bookid, 'status', 'confirmed');

            $title ='Confirmed Appointment    Booking ID:'.$bookid;
            $content = 'Appointment was confirmed automatically';
            $pushdata = array(
                "notification_id" => "16",
                "alert" => $content,);
            $onetoken = $seller->device_token;
            $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);

            $content_str = '<b>'.$seller->first_name.' '.$seller->last_name.'</b> confirmed your appointment for <b>'.$mas_type->name.'</b> massage <br> Today at '.$bdata->start_time.', duration '.$bdata->duration.' minutes at $'.$bdata->cost.' rate';
            $ins_event = array(
                'user_id' => $buyerid,
                'content' => $content_str,
                'book_id' => $bookid,
                'type_id' => '1',
                'isread' => '0',
                'event_time' => $book_time,
            );
            $this->events->insert($ins_event);

            $title ='Confirmed Appointment    Booking ID:'.$bookid;
            $content = $seller->first_name.' '.$seller->last_name.'</b> confirmed appointment for <b>'.$mas_type->name.'</b> massage ';
            $pushdata = array(
                "notification_id" => "1",
                "alert" => $content,
                'book_id' => $bookid,
                );
            $onetoken = $buyer->device_token;
            $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);

        } else{
            $content_str = '<b>'.$buyer->first_name.' '.$buyer->last_name.'</b> wants to book appointment for <b>'.$mas_type->name.'</b> massage <br> Today at '.$bdata->start_time.', duration '.$bdata->duration.' minutes';
            $ins_event = array(
                'user_id' => $bdata->seller_id,
                'content' => $content_str,
                'book_id' => $bookid,
                'type_id' => '11',
                'isread' => '0',
                'event_time' => $book_time,
            );
            $this->events->insert($ins_event);

            $title ='Request Appointment    Booking ID:'.$bookid;
            $content = $buyer->first_name.' '.$buyer->last_name.' wants to book appointment for <b>'.$mas_type->name.'</b> massage ';
            $pushdata = array(
                "notification_id" => "11",
                "alert" => $content,);
            $onetoken = $seller->device_token;
            $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);
        }

        if(!empty($bdata->seller_note)){
            $ins_data = array(
                'sender_id' => $bdata->seller_id,
                'receiver_id' => $buyerid,
                'content' => $bdata->seller_note,
                'send_time' => $book_time,
                'book_id' => $bookid,
                'read_status' => '0',
            );
            $this->messages->insert($ins_data);

            $content_str = 'You have a new message from <b>'.$seller->first_name.' '.$seller->last_name.'</b>';
            $ins_event = array(
                'user_id' => $buyerid,
                'content' => $content_str,
                'book_id' => $bookid,
                'type_id' => '4',
                'isread' => '0',
                'event_time' => $book_time,
            );
            $this->events->insert($ins_event);

            $title ='Send Message    Booking ID:'.$bookid;
            $content = $seller->first_name.' '.$seller->last_name.' send you a message';
            $pushdata = array(
                "notification_id" => "4",
                "alert" => $content,);
            $onetoken = $buyer->device_token;
            $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);
        }
        if(!empty($bnote)){
            $ins_mes = array(
                'sender_id' => $buyerid,
                'receiver_id' => $seller->id,
                'content' => $bnote,
                'send_time' => $book_time,
                'book_id' => $bookid,
                'read_status' => '0',
            );
            $this->messages->insert($ins_mes);

            $content_str = '<b>'.$buyer->first_name.' '.$buyer->last_name.'</b> send you a message';
            $ins_event = array(
                'user_id' => $seller->id,
                'content' => $content_str,
                'book_id' => $bookid,
                'type_id' => '14',
                'isread' => '0',
                'event_time' => $book_time,
            );
            $this->events->insert($ins_event);

            $title ='Send Message    Booking ID:'.$bookid;
            $content = $buyer->first_name.' '.$buyer->last_name.' send you a message';
            $pushdata = array(
                "notification_id" => "14",
                "alert" => $content,);
            $onetoken = $seller->device_token;
            $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);
        }

        $res_data = array(
            'status' => 'success',
            'data' => $result,
        );
        $this->response($res_data);
    }

    public function confirmAppointment_post(){
        $bookid = $this->post('book_id');
        $confirm_time = $this->post('confirm_time');
        //$eventid = $this->post('event_id');

        //$this->events->update_field($eventid, 'isread', '1');
        $this->events->setReadRequestEvent($bookid);
        $this->books->update_field($bookid, 'status', 'confirmed');
        $bdata = $this->books->fetch_for_id($bookid);
        $seller = $this->users->fetch_for_id($bdata->seller_id);
        $buyer = $this->users->fetch_for_id($bdata->buyer_id);
        $mas_type = $this->massages->fetch_for_id($bdata->massage_type);

        $content_str = '<b>'.$seller->first_name.' '.$seller->last_name.'</b> confirmed your appointment for <b>'.$mas_type->name.'</b> massage <br> Today at '.$bdata->start_time.', duration '.$bdata->duration.' minutes at $'.$bdata->cost.' rate';
        $ins_event = array(
            'user_id' => $buyer->id,
            'content' => $content_str,
            'book_id' => $bookid,
            'type_id' => '1',
            'isread' => '0',
            'event_time' => $confirm_time,
        );
        $this->events->insert($ins_event);

        $title ='Confirmed Appointment    Booking ID:'.$bookid;
        $content = $seller->first_name.' '.$seller->last_name.' confirmed appointment for <b>'.$mas_type->name.'</b> massage ';
        $pushdata = array(
            "notification_id" => "1",
            "alert" => $content,
            "book_id" => $bookid,
            );
        $onetoken = $buyer->device_token;
        $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);

        $res_data = array(
            'status' => 'success',
            'data' => $result,
        );
        $this->response($res_data);
    }

    public function cancelAppointment_post(){
        $bookid = $this->post('book_id');
        $bdata = $this->books->fetch_for_id($bookid);
        $this->books->update_field($bookid, 'buyer_id', '0');
        $this->books->update_field($bookid, 'massage_type', '');
        $this->books->update_field($bookid, 'buyer_note', '');
        $this->books->update_field($bookid, 'book_time', '');
        $this->books->update_field($bookid, 'status', 'posted');

        $this->events->delete_for_bookid($bookid);
        $this->messages->delete_for_bookid($bookid);

        $seller = $this->users->fetch_for_id($bdata->seller_id);
        $title ='Canceled Appointment    Booking ID:'.$bookid;
        $content = 'User cancelled this appointment.';
        $pushdata = array(
            "notification_id" => "15",
            "alert" => $content,);
        $onetoken = $seller->device_token;
        $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);

        $res_data = array(
            'status' => 'success',
            'data' => '',
        );
        $this->response($res_data);
    }

    public function rejectAppointment_post(){
        $bookid = $this->post('book_id');
        $bdata = $this->books->fetch_for_id($bookid);
        if($bdata->status == "requested"){
            $this->books->update_field($bookid, 'buyer_id', '0');
            $this->books->update_field($bookid, 'massage_type', '');
            $this->books->update_field($bookid, 'buyer_note', '');
            $this->books->update_field($bookid, 'book_time', '');
            $this->books->update_field($bookid, 'status', 'posted');

            $this->events->delete_for_bookid($bookid);
            $this->messages->delete_for_bookid($bookid);

            $buyer = $this->users->fetch_for_id($bdata->buyer_id);
            $title ='Rejected Appointment    Booking ID:'.$bookid;
            $content = 'This appointment was rejected.';
            $pushdata = array(
                "notification_id" => "5",
                "alert" => $content,);
            $onetoken = $buyer->device_token;
            $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);

            $res_data = array(
                'status' => 'success',
                'data' => '',
            );
            $this->response($res_data);
        } else{
            $res_data = array(
                'status' => 'failed',
                'error' => 'You can not reject this Appointment.',
            );
            $this->response($res_data);
        }
    }

    public function getEvents_post(){
        $userid = $this->post('uid');
        $event_data = $this->events->getUnreadEvents($userid);
        $user = $this->users->fetch_for_id($userid);
        foreach($event_data as $d){
            $dbook = $this->books->fetch_for_id($d->book_id);
            if($user->type == '1'){//customer
                $seller = $this->users->fetch_for_id($dbook->seller_id);
                $d->photo = $seller->photo;
                $d->sender_id = $seller->id;
            } else{//therapist
                $customer = $this->users->fetch_for_id($dbook->buyer_id);
                $d->photo = $customer->photo;
                $d->sender_id = $customer->id;
            }
            $d->book_status = $dbook->status;
        }
        $res_data = array(
            'status' => 'success',
            'data' => $event_data,
        );
        $this->response($res_data);
    }

    public function setEventRead_post(){
        $eventid = $this->post('event_id');
        $this->events->update_field($eventid, 'isread', '1');
        $res_data = array(
            'status' => 'success',
            'data' => '',
        );
        $this->response($res_data);
    }

    public function getAppointment_post(){
        $bookid = $this->post('book_id');

        $bdata = $this->books->fetch_for_id($bookid);
        $seller = $this->users->fetch_for_id($bdata->seller_id);
        $buyer = $this->users->fetch_for_id($bdata->buyer_id);
        $bs_data = $this->business_infos->get_where('user_id',$seller->id);
        $bdata->bs_address = $seller->address;
        $bdata->bs_location = $seller->location;
        $bdata->bs_name = $bs_data[0]->name;
        $bdata->seller_rate = $seller->rate;
        $bdata->seller_photo = $seller->photo;
        $bdata->seller_phone = $seller->phone;
        $bdata->seller_name = $seller->first_name.' '.$seller->last_name;
        $bdata->buyer_name = $buyer->first_name.' '.$buyer->last_name;
        $bdata->buyer_photo = $buyer->photo;
        $bdata->buyer_rate = $buyer->rate;
        $res_data = array(
            'status' => 'success',
            'data' => $bdata,
        );
        $this->response($res_data);
    }

    public function getCustomerProfile_post(){
        $userid = $this->post('uid');
        $bookid = $this->post('book_id');
        $retlist = $this->reviews->get_where("user_id", $userid);
        foreach($retlist as $d){
            $user_one = $this->users->fetch_for_id($d->given_id);
            $d->first_name = $user_one->first_name;
            $d->last_name = $user_one->last_name;
        }

        $udata = $this->users->fetch_for_id($userid);
        $bdata = $this->books->fetch_for_id($bookid);

        $retdata = array(
            'user' => $udata,
            'book' => $bdata,
            'reviews' => $retlist,
        );
        $resdata = array(
            'status' => 'success',
            'data' => $retdata,
        );
        $this->response($resdata);
    }

    public function reportReview_post(){
        $toid = $this->post('to_user');
        $fromid = $this->post('from_user');
        $rate = $this->post('rate');
        $bookid = $this->post('book_id');
        $review = $this->post('review');
        $postdate = $this->post('post_date');
        $posttime = $this->post('post_time');

        $touser = $this->users->fetch_for_id($toid);
        $rate_val = floatval($rate);
        $origin_rate = floatval($touser->rate);
        $origin_count = intval($touser->rate_count);
        $new_ranking = ($rate_val + $origin_rate * $origin_count) / ($origin_count + 1);
        $new_count = $origin_count + 1;
        $this->users->update_field($toid, 'rate', $new_ranking);
        $this->users->update_field($toid, 'rate_count', $new_count);
        if(!empty($review)){
            $ins_data = array(
                'user_id' => $toid,
                'given_id' => $fromid,
                'rate' => $rate,
                'comment' => $review,
                'postdate' => $postdate,
            );

            $this->reviews->insert($ins_data);
        }

        if($touser->type == '1'){
            $this->events->setReadSellerFinishEvent($bookid);
            $type_id = '3';
        } else{
            $this->events->setReadFinishEvent($bookid);
            $type_id = '13';
        }

        $fromuser = $this->users->fetch_for_id($fromid);
        $content_str = '<b>'.$fromuser->first_name.' '.$fromuser->last_name.'</b> gave you a '.$rate.' star rating.';
        $ins_event = array(
            'user_id' => $toid,
            'content' => $content_str,
            'book_id' => $bookid,
            'type_id' => $type_id,
            'isread' => '0',
            'event_time' => $posttime,
        );
        $this->events->insert($ins_event);

        $title ='Rating     Booking ID:'.$bookid;
        $pushdata = array(
            "notification_id" => "3",
            "alert" => $content_str,);
        $onetoken = $touser->device_token;
        $result = $this->send_push_notification_by_device($onetoken, "", $title, $content_str, $pushdata, 1);

        $resdata = array(
            'status' => 'success',
            'data' => '',
        );
        $this->response($resdata);
    }

    public function getPaymentInfo_post(){
        $bookid = $this->post('book_id');

        $bdata = $this->books->fetch_for_id($bookid);
        $seller = $this->users->fetch_for_id($bdata->seller_id);
        $buyer = $this->users->fetch_for_id($bdata->buyer_id);
        $bs_data = $this->business_infos->get_where('user_id',$seller->id);
        $bdata->bs_address = $seller->address;
        $bdata->bs_location = $seller->location;
        $bdata->bs_name = $bs_data[0]->name;
        $bdata->seller_rate = $seller->rate;
        $bdata->seller_photo = $seller->photo;
        $bdata->seller_name = $seller->first_name.' '.$seller->last_name;
        $bdata->seller_phone = $seller->phone;

        $ret_data = array(
            'seller_stripe_id' => $seller->stripe_id,
            'book' => $bdata,
        );
        $res_data = array(
            'status' => 'success',
            'data' => $ret_data,
        );
        $this->response($res_data);
    }

    public function doPay_post(){
        $event_id = $this->post('event_id');
        $book_id = $this->post('book_id');
        $paytime = $this->post('paytime');
        $chargeid = $this->post('charge_id');
        $card_name = $this->post('card_name');
        $ch_status = $this->post('status');

        $this->events->update_field($event_id, 'isread', '1');
        $this->events->setReadMessageEvent($book_id);
        $this->messages->delete_for_bookid($book_id);

        $this->books->update_field($book_id, 'status', 'finished');
        $bdata = $this->books->fetch_for_id($book_id);
        $seller = $this->users->fetch_for_id($bdata->seller_id);
        $buyer = $this->users->fetch_for_id($bdata->buyer_id);
        $mas_type = $this->massages->fetch_for_id($bdata->massage_type);

        $content_str = 'Appointment finished. Please rate <b>'.$buyer->first_name.' '.$buyer->last_name.'</b> <br> Today at '.$bdata->start_time.', duration '.$bdata->duration.' minutes, '.$mas_type->name.' massage';
        $ins_event = array(
            'user_id' => $seller->id,
            'content' => $content_str,
            'book_id' => $book_id,
            'type_id' => '12',
            'isread' => '0',
            'event_time' => $paytime,
        );
        $this->events->insert($ins_event);

        $content_str = 'Appointment finished. Please rate <b>'.$seller->first_name.' '.$seller->last_name.'</b> <br> Today at '.$bdata->start_time.', '.$mas_type->name.' massage, duration '.$bdata->duration.' minutes at $'.$bdata->cost.' rate';
        $ins_event = array(
            'user_id' => $buyer->id,
            'content' => $content_str,
            'book_id' => $book_id,
            'type_id' => '2',
            'isread' => '0',
            'event_time' => $paytime,
        );
        $this->events->insert($ins_event);

        $title ='Finished Appointment    Booking ID:'.$book_id;
        $content ='Appointment finished';
        $pushdata = array(
            "notification_id" => "12",
            "alert" => $content,);
        $onetoken = $seller->device_token;
        $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);
        $pushdata = array(
            "notification_id" => "2",
            "alert" => $content,);
        $onetoken1 = $buyer->device_token;
        $result = $this->send_push_notification_by_device($onetoken1, "", $title, $content, $pushdata, 1);

        $pay_data = array(
            'book_id' => $book_id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'price' => $bdata->cost,
            'paytime' => $paytime,
            'charge_id' => $chargeid,
            'card_name' => $card_name,
            'status' => $ch_status,
        );
        $this->payments->insert($pay_data);

        $res_data = array(
            'status' => 'success',
            'data' => '',
        );
        $this->response($res_data);

    }

    public function getBookHistory_post(){
        $userid = $this->post('user_id');
        $bdata = $this->books->where(array('buyer_id' => $userid, 'status' => 'finished'))->get_all();
        foreach($bdata as $one){
            $seller = $this->users->fetch_for_id($one->seller_id);
            $bs_data = $this->business_infos->get_where('user_id',$seller->id);
            $one->bs_address = $seller->address;
            $one->bs_location = $seller->location;
            $one->bs_name = $bs_data[0]->name;
            $one->seller_rate = $seller->rate;
            $one->seller_photo = $seller->photo;
            $one->seller_name = $seller->first_name.' '.$seller->last_name;
            $one->seller_phone = $seller->phone;
        }

        $res_data = array(
            'status' => 'success',
            'data' => $bdata,
        );
        $this->response($res_data);
    }

    public function getMessages_post(){
        $bookid = $this->post('book_id');
        $bdata = $this->books->fetch_for_id($bookid);
        $this->events->setReadMessageEvent($bookid);
        $message_data = $this->messages->get_where('book_id', $bookid);
        $seller = $this->users->fetch_for_id($bdata->seller_id);
        $buyer = $this->users->fetch_for_id($bdata->buyer_id);

        $ret_data = array(
            'seller_name' => $seller->first_name.' '.$seller->last_name,
            'buyer_name' => $buyer->first_name.' '.$buyer->last_name,
            'messages' => $message_data,
        );

        $res_data = array(
            'status' => 'success',
            'data' => $ret_data,
        );
        $this->response($res_data);
    }

    public function sendPrivateMessage_post(){
        $bookid = $this->post('book_id');
        $sender_id = $this->post('sender_id');
        $receiver_id = $this->post('receiver_id');
        $content = $this->post('content');
        $send_time = $this->post('send_time');

        $this->messages->setReadMessages($bookid, $receiver_id);

        $ins_data = array(
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'content' => $content,
            'send_time' => $send_time,
            'book_id' => $bookid,
            'read_status' => '0',
        );

        $this->messages->insert($ins_data);

        $receiver = $this->users->fetch_for_id($receiver_id);

        $content_str = 'You have a new message from <b>'.$receiver->first_name.' '.$receiver->last_name.'</b>';


        $title ='Send Message    Booking ID:'.$bookid;
        $content = $receiver->first_name.' '.$receiver->last_name.' send you a message';
        if($receiver->type == '1'){
            $ins_event = array(
                'user_id' => $receiver_id,
                'content' => $content_str,
                'book_id' => $bookid,
                'type_id' => '4',
                'isread' => '0',
                'event_time' => $send_time,
            );
            $this->events->insert($ins_event);

            $pushdata = array(
                "notification_id" => "4",
                "alert" => $content,);
            $onetoken = $receiver->device_token;
            $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);
        } else{
            $ins_event = array(
                'user_id' => $receiver_id,
                'content' => $content_str,
                'book_id' => $bookid,
                'type_id' => '14',
                'isread' => '0',
                'event_time' => $send_time,
            );
            $this->events->insert($ins_event);

            $pushdata = array(
                "notification_id" => "14",
                "alert" => $content,);
            $onetoken = $receiver->device_token;
            $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);
        }

        $res_data = array(
            'status' => 'success',
            'data' => '',
        );
        $this->response($res_data);
    }

    public function getPaymentHistory_post(){
        $userid = $this->post('uid');
        $paydata = $this->payments->get_where('seller_id', $userid);
        foreach($paydata as $one){
            $bdata = $this->books->fetch_for_id($one->book_id);
            $one->book_date = $bdata->start_date;
            $one->book_time = $bdata->start_time;
            $one->duration = $bdata->duration;
            $one->massage_type = $bdata->massage_type;
        }

        $res_data = array(
            'status' => 'success',
            'data' => $paydata,
        );
        $this->response($res_data);
    }

    public function send_push_notification($fields) {
        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . ONE_SIGNAL_REST_API_KEY));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function send_push_notification_all($title, $content, $data) {
        $title = array(
            "en" => $title
        );
        $content = array(
            "en" => $content
        );

        $fields = array(
            'app_id' => ONE_SIGNAL_APP_ID,
            'included_segments' => array('All'),
            'headings' => $title,
            'contents' => $content,
            'data' => $data,
        );

        return $this->send_push_notification($fields);
    }

    public function send_push_notification_by_filters($filters, $title, $content, $data) {
        $title = array(
            "en" => $title
        );
        $content = array(
            "en" => $content
        );

        $fields = array(
            'app_id' => ONE_SIGNAL_APP_ID,
            'filters' => $filters,
            'headings' => $title,
            'contents' => $content,
            'data' => $data,
        );

        return $this->send_push_notification($fields);
    }

    public function send_push_notification_by_devices($device_ids, $title, $content, $data) {
        $title = array(
            "en" => $title
        );
        $content = array(
            "en" => $content
        );

        $fields = array(
            'app_id' => ONE_SIGNAL_APP_ID,
            'include_player_ids' => $device_ids,
            'headings' => $title,
            'contents' => $content,
            'data' => $data,
        );

        return $this->send_push_notification($fields);
    }

    public function send_push_notification_by_device($ios_device_id, $android_device_id, $title, $content, $data, $badge_count) {
        $title = array(
            "en" => $title
        );
        $content = array(
            "en" => $content
        );

        $device_ids = array();

        if(!empty($ios_device_id))
            $device_ids[] = $ios_device_id;

        if(!empty($android_device_id))
            $device_ids[] = $android_device_id;

        $fields = array(
            'app_id' => ONE_SIGNAL_APP_ID,
            'include_player_ids' => $device_ids,
            'headings' => $title,
            'contents' => $content,
            'data' => $data,
            'ios_badgeType' => 'SetTo',
            'ios_badgeCount' => $badge_count,
        );

        return $this->send_push_notification($fields);
    }

    public function uploadPayInfo_post(){
        $bookId = $this->post('book_id');
        $type = $this->post('type');
        $trans_id = $this->post('trans_id');
        $paytime = $this->post('time');
        $charge_id = $this->post('charge_id');
        $status = $this->post('status');
        $amount = $this->post('amount');

        $predata = $this->payments->where(array('book_id' => $bookId, 'trans_id' => $trans_id))->get_all();
        if(count($predata)){
            $result = array(
                'status' => 'success',
                'data' => $predata[0]->id,
            );
            $this->response($result);
        } else{
            $bdata = $this->books->fetch_for_id($bookId);
            $user = $this->users->fetch_for_id($bdata->user_id);
            $pilot = $this->users->fetch_for_id($bdata->pilot_id);
            $inputdata = array(
                'book_id' => $bookId,
                'user_id' => $bdata->user_id,
                'user_name' => $user->username,
                'pilot_id' => $bdata->pilot_id,
                'pilot_name' => $pilot->username,
                'type' => $type,
                'price' => $bdata->price,
                'amount' => $amount,
                'paytime' => $paytime,
                'trans_id' => $trans_id,
                'charge_id' => $charge_id,
                'status' => $status,
            );

            $payId = $this->payments->insert($inputdata);
            $this->books->update_field($bookId, 'status_id', '4');

            $title ='Booking Paid - Booking ID:'.$bookId;
            $content = $user->username.' has made payment for booking - Charge status '.$status;
            $pushdata = array(
                "notification_id" => "4",
                "alert" => $user->username.' has made payment for booking - Charge status '.$status);
            $onetoken = $pilot->device_token;
            $result = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);

            $time = $this->post('publishtime');//date("m-d-Y H:i A");
            $ins_data = array(
                'book_id' => $bookId,
                'content' => $user->username.' paid to '.$pilot->username,
                'isread' => 0,
                'publish_time' => $paytime,
            );
            $this->event_posts->insert($ins_data);

            $result = array(
                'status' => 'success',
                'data' => $payId,
            );
            $this->response($result);
        }
    }

    public function unchecked_notificatios_get() {
        $this->load->model('Event_post_model', 'event_posts');
        $unchecked_notifications = $this->event_posts->set_where(array('isread' => '0'))->get_all();

        $result = array('status' => false);
        if(count($unchecked_notifications)>0) {
            $result = array('status' => true);
        }
        $this->response($result);
    }

    public function pending_notifications_get() {
        $this->load->model('Event_post_model', 'event_posts');
        $unchecked_notifications = $this->event_posts->set_where(array('isread' => '0'))->get_all();

        $visitors = array();
        foreach ($unchecked_notifications as $unchecked_one) {
            $this->event_posts->update_field($unchecked_one->id, 'isread', '1');
            $item = array(
                'content' => $unchecked_one->content,
            );
            $visitors[] = $item;
        }
        $this->response($visitors);
    }

    public function completedBook_post(){
        $bookId = $this->post('book_id');
        $bdata = $this->books->fetch_for_id($bookId);
        $this->books->update_field($bookId, 'status_id', '6');
        $user = $this->users->fetch_for_id($bdata->user_id);
        $pilot = $this->users->fetch_for_id($bdata->pilot_id);
        $time = date("m d, Y H:i A");

        $ins_data = array(
            'book_id' => $bookId,
            'content' => $pilot->username.' completed this flight(Booking ID : '.$bookId.').',
            'isread' => 0,
            'publish_time' => $time,
        );
        $this->event_posts->insert($ins_data);

        $content = 'Your flight has been completed and confirmed by pilot.';//$pilot->username.' completed this flight.';
        $pushdata = array(
            "notification_id" => "6",
            "alert" => 'Your flight has been completed and confirmed by pilot.',
            'book_id' => $bookId);
        $onetoken = $user->device_token;
        $email = $user->email;
        $title ='Completed Flight    Booking ID:'.$bookId;
        $res = $this->send_push_notification_by_device($onetoken, "", $title, $content, $pushdata, 1);


        $to = $email; // Send email to our user
        $subject = 'Completed Flight'; // Give the email a subject
        $message = '<center>
  				<h2>Completed Flight</h2><br>
				<p>Your flight has been completed and confirmed by pilot.</p>

            	<br>
            	-------------------------------------

            	<h4>Thanks for choosing Skytaxi.</h4>
				</center>';

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: info@skytaxiapp.com' . "\r\n" .
            'Reply-To: info@skytaxiapp.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();// Set from headers
        mail($to, $subject, $message, $headers); // Send our email

        $result = array(
            'status' => 'success',
            'data' => '',
        );
        $this->response($result);
    }
}
