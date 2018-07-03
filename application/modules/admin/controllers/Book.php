<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Book extends Admin_Controller
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
        $crud = $this->generate_crud('book_posts');
        $crud->columns('id', 'seller_id', 'buyer_id', 'massage_type', 'start_date', 'start_time', 'duration', 'cost', 'book_time', 'status');
        $crud->set_relation('seller_id', 'users', 'first_name');
        $crud->display_as('seller_id','Therapist');

        $crud->where('status','finished');
        $crud->order_by('start_date', 'desc');
        $crud->set_relation('buyer_id', 'users', 'first_name');
        $crud->set_relation('massage_type', 'massage_types', 'name');
        $crud->display_as('buyer_id','Customer');
        $crud->display_as('status','Status');
        $crud->display_as('massage_type','Massage Type');
        $crud->display_as('start_date','Book Date');
        $crud->display_as('start_time','Book Time');
        $crud->display_as('duration','Duration');
        $crud->display_as('cost','Cost');
        $crud->display_as('book_time','Appointment Time');
        //$crud->set_relation('category_id', 'product_blog_categories', 'title');
        //$crud->set_relation_n_n('tags', 'product_blog_posts_tags', 'product_blog_tags', 'post_id', 'tag_id', 'title');
        /*if ($this->ion_auth->in_group(array('webmaster')))
        {
            $crud->display_as('seller_id','Therapist');

        } else if($this->mUser->id){
            $crud->where('seller_id', $this->mUser->id);

            $crud->callback_after_insert(array($this, 'update_date_after_insert'));

        } else {
            $crud->unset_operations();
        }*/

        $crud->unset_add();
        $crud->unset_edit();
        $this->mTitle = 'Books';
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
        $this->books->update_field($primary_key, 'seller_id', $this->mUser->id);
        return true;
    }

    // Grocery CRUD - Blog Categories
    public function blog_category()
    {
        $crud = $this->generate_crud('massage_types');
        $crud->columns('title');
        $this->mTitle .= 'Massage Types';
        $this->mViewData['crud_note'] = modules::run('adminlte/widget/btn', 'Sort Type', 'book/blog_category_sortable');
        $this->render_crud();
    }

    // Sortable - Blog Categories
    public function blog_category_sortable()
    {
        $this->load->library('sortable');
        $this->sortable->init('massage_type_model');
        $this->mViewData['content'] = $this->sortable->render('{title}', 'book/blog_category');
        $this->mTitle .= 'Massage Types';
        $this->render('general');
    }

    // Simple page with parameter
    public function item($product_id)
    {
        $this->mTitle .= 'Booking ID ' . $product_id;
        $this->mViewData['id'] = $product_id;
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
