<?php

class UserController extends Controller 
{
    public function index()
    {
        $user = $this->model('UserModel');
        $data = $user->index();
        $title = "Quản lí user";
        $this->view('AdminUser/index', [
            'user' => $data,
            'title' => $title
        ]);
    }
}
