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

    /**
     * @deprecated 
     */
    function setPageInfo($title, $category, $subCategory)
    {
        $this->title = $title;
        $this->category = $category;
        $this->subCategory = $subCategory;
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