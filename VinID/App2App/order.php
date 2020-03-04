<?php

namespace VinID;

class Order implements \JsonSerializable
{
    private $meta;
    private $data;

    public function __construct($meta, $data)
    {
        $this->meta = $meta;
        $this->data = $data;
    }

    public function getOrderID() {
        return $this->data->order_id;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}