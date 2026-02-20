<?php

class WishlistController extends \Controller
{
    public function __construct()
    {
        $this->checkAdmin();
    }
}
