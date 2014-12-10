<?php

class InventoryController extends BaseController
{

    public function index()
    {
        return;
    }

    public function supplierForm()
    {
        return View::make('inventory.supplierform');
    }

    public function addSupplier()
    {
        $country = Input::Get('country');
        $state = Input::Get('state');
        $suppliername = Input::Get('suppliername');
        $contactname = Input::Get('contactname');
        $address1 = Input::Get('address1');
        $address2 = Input::Get('address2');
        $city = Input::Get('city');
        $zip = Input::Get('zip');
        $email = Input::Get('email');
        $site = Input::Get('site');
        $code = Input::Get('code');
        $phone = Input::Get('phone');

        $query = "INSERT INTO [Supplier]
                  (Country, LastUpdated, State, SupplierName,
                   ContactName, Address1, Address2, City, Zip, EmailAddress,
                   WebPageAddress, Code, PhoneNumber)

                  VALUES ('$country', GetDate(), '$state', '$suppliername',
                  '$contactname', '$address1', '$address2', '$city', '$zip', '$email',
                  '$site', '$code', '$phone');";

        DB::connection('mssql-squareone')->insert($query);
        DB::connection('mssql-toronto')->insert($query);

        return View::make('inventory.supplierform');
    }
}