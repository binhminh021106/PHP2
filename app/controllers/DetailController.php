<?php

class DetailController extends Controller
{
    public function detail($id)
    {
        $productDetail = $this->model('ProductModel');
        $productDetail->getById($id);
        $title = $productDetail['name'];
        $this->view('home.detail', [
            'productDetail' => $productDetail,
            'title' => $title
        ]);
    }
}