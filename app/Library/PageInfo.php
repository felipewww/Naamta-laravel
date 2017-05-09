<?php

namespace App\Library;

trait PageInfo{
    public $pageInfo;

    public $title       = 'Default title';
    public $category    = 'Default Category';
    public $subCategory = 'Default Sub Category';

    public function __construct()
    {
        $this->category     = new Category();
        $this->subCategory  = new SubCategory();
        return $this;
    }
}

class Category{
    public $title = 'Default Category';
    public $link = '#';
}

class SubCategory{
    public $title = 'Default Category';
    public $link = '#';
}