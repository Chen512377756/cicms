<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );
class Procart extends Controller {
    var $title = 'procart';
    var $view = 'procart';
    var $table = 'product';
    function Procart() {
        parent::Controller ();
        $this->load->model ( "dataset_atom", "my_model" );
        $this->my_model->table_name ( $this->table );
    }
    function index() {
        $this->load->library ( 'cart' );
        $this->load->view ( $this->view, get_defined_vars () );
    }
    function cart() {
        $id = $_POST ['id'];
        $size = $_POST ['size'];
        $price = $_POST ['price'];
        $number = $_POST ['number'];
        if ($id) {
            $pro = $this->db->get_where ( "product", array (
                'id' => $id
            ) )->row ();
            $this->load->library('cart');
            $i = 0;
            foreach ( $this->cart->contents () as $k => $v ) {
                if ($id != $v ['id'])
                    continue;
                if ($id == $v ['id'] && $size == $v ['options'] ['size']) {
                    $i = $v ['qty'];
                }
            }
            $data = array (
                'id' => $id,
                'qty' => $i + $number,
                'price' => $price,
                'name' => 123,
                'options' => array (
                    'pic1' => $pro->pic1,
                    'size' => $size,
                    'title' => $pro->title
                )
            );
            $this->cart->insert($data);
            //print_r($data);exit;
        }
        echo '<script>window.location.href="procart.php?index/' . $id . '"</script>';
    }
    function update() {
        $this->load->library ( 'cart' );
        $rowid = $this->uri->segment ( 3 );
        $data = array (
            'rowid' => $rowid,
            'qty' => 0
        );
        // print_r($this->cart->contents()).'<br>';
        $this->cart->update ( $data );
    }
    function destroy() {
        $this->load->library ( 'cart' );
        $this->cart->destroy ();
        redirect ( 'procart' );
    }
    function del() {
        $this->load->library ( 'cart' );
        $id = $_POST ['id'];
        // echo $id;exit;
        foreach ( $this->cart->contents () as $k => $v ) {
            $rid = $v ['rowid'];
            if ($id == $rid) {
                $data = array (
                    'rowid' => $rid,
                    'qty' => 0
                );
            }
        }
        $this->cart->update ( $data );
    }
    function post() {
        $this->load->library ( 'cart' );
        $this->load->library ( 'form_validation', $this->rule_post );
        if (! $this->form_validation->run ()) {
            redirect ( 'procart' );
        }
        $form = $this->form_validation->to_array ();
        $form ['timeline'] = time ();
        // /print_r($this->cart->contents());

        foreach ( $this->cart->contents () as $k => $v ) {
            $title [] = $v ['name'];
        }
        if (is_array ( $title )) {
            $title = implode ( $title, '@@' );
        }
        $form ['product_title'] = $title;

        $this->load->database ();
        if (! $this->db->insert ( "order", $form )) {
            $this->form_validation->set_error ( 'username', $this->lang->line ( 'item_put_failure' ) );
            redirect ( 'procart' );
        }
        if (! ($ins = $this->db->insert_id ())) {
            return false;
        }
        $this->db->update ( "order", array (
            'sort_id' => $ins
        ), array (
            'id' => $ins
        ) );

        goto_message ( $this->lang->line ( 'site_post_success' ), 'procart' );
    }
    function num() {
        $this->load->library ( 'cart' );
        $id = $_POST ['id'];
        $qty = $_POST ['value'];
        foreach ( $this->cart->contents () as $k => $v ) {
            if ($id != $v ['rowid'])
                continue;
            $rid = $v ['rowid'];
            $data = array (
                'rowid' => $rid,
                'qty' => $qty
            );
            $this->cart->update ( $data );
        }
    }
}