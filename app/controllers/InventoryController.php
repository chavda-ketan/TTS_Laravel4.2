<?php

class InventoryController extends BaseController
{

    public function index()
    {
        return $this->showRepeatCustomerReport();
    }

}