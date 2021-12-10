<?php


namespace App\Filters;


    class ProductFilters extends QueryFilter
{
    public function name($query = "")
    {
        return $this->builder->where('name', 'like', '%'.$query.'%');
    }

    public function price($query = "")
    {
        return $this->builder->where('price', 'like', '%'.$query.'%');
    }

    public function upc($query = "")
    {
        return $this->builder->where('upc', 'like', '%'.$query.'%');
    }

    public function status($query = null)
    {
        return $this->builder->where('status', $query);
    }
}
