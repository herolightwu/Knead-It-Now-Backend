<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->mTitle = 'Book - ';
        $this->push_breadcrumb('book');

        $site_config = $this->config->item('site');
    }

    public function index()
    {
        $crud = $this->generate_crud('payment_posts');
        //if ($this->ion_auth->in_group(array('webmaster'))) {
        $crud->columns('book_id', 'buyer_id', 'seller_id', 'price', 'paytime', 'charge_id', 'status');
        $crud->display_as('buyer_id','Customer');
        $crud->display_as('seller_id','Thrapist');
        $crud->display_as('charge_id','Charge Id');
        $crud->display_as('status','Pay Status');
        $crud->set_relation('buyer_id', 'users', 'first_name');
        $crud->set_relation('seller_id', 'users', 'first_name');

        $crud->order_by('paytime', 'desc');
        $crud->display_as('price','Price($)');
        $crud->display_as('paytime','Payed Time');
        $crud->display_as('book_id','Booking ID');

        $crud->unset_add();
        $crud->unset_edit();
        $this->mTitle = 'Payment Transactions';
        $this->render_crud();
    }

    // AdminLTE Components
    public function adminlte()
    {
        $this->mTitle .= 'AdminLTE Components';
        $this->render('product/adminlte');
    }

    // Grocery CRUD - Blog Posts
    public function blog_post()
    {

    }

    public function update_date_after_insert($post_array ,$primary_key)
    {
        $this->products->update_field($primary_key, 'seller_id', $this->mUser->user_id);
        return true;
    }

    public function callback_user_email($value, $row){
        $this->load->model('User_model', 'users');

        return $this->users->get($row->user_id)->email;
    }

    // Grocery CRUD - Blog Categories
    public function blog_category()
    {
        $crud = $this->generate_crud('product_blog_categories');
        $crud->columns('title');
        $this->mTitle .= 'Blog Categories';
        $this->mViewData['crud_note'] = modules::run('adminlte/widget/btn', 'Sort Order', 'product/blog_category_sortable');
        $this->render_crud();
    }

    // Sortable - Blog Categories
    public function blog_category_sortable()
    {
        $this->load->library('sortable');
        $this->sortable->init('product_blog_category_model');
        $this->mViewData['content'] = $this->sortable->render('{title}', 'product/blog_category');
        $this->mTitle .= 'Blog Categories';
        $this->render('general');
    }

    // Grocery CRUD - Blog Tags
    public function blog_tag()
    {
        $crud = $this->generate_crud('product_blog_tags');
        $crud->set_field_upload('icon', UPLOAD_BLOG_TAG);

        $this->mTitle .= 'Blog Tags';
        $this->render_crud();
    }


    // Simple page with parameter
    public function item($product_id)
    {
        $this->mTitle .= 'Item ' . $product_id;
        $this->mViewData['product_id'] = $product_id;
        $this->render('product/item');
    }

    // Pagination widget
    public function pagination()
    {
        $this->load->library('pagination');
        $this->mViewData['pagination'] = $this->pagination->render(200, 20);
        $this->mTitle .= 'Pagination';
        $this->render('product/pagination');
    }

    // Sortable widget
    public function sortable()
    {
        $this->mViewData['entries'] = array(
            'Item 1', 'Item 2', 'Item 3', 'Item 4', 'Item 5'
        );
        $this->mTitle .= 'Sortable';
        $this->render('product/sortable');
    }
}