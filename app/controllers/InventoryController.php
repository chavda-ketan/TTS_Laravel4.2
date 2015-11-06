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

/**
Inventory Transfers
 */
    public function inventoryTransferList()
    {
        $data['transfers'] = DB::connection('mysql')->select('SELECT * FROM inventory_transfer
                                                              ORDER BY id DESC');
        return View::make('inventory.transfer.list', $data);
    }

    public function inventoryTransferCreate()
    {
        $query = "SELECT * FROM Department ORDER BY Name ASC";
        $departments = DB::connection('mssql-squareone')->select($query);

        $data['departments'] = $departments;

        return View::make('inventory.transfer.create', $data);
    }

    public function inventoryTransferView($id)
    {
        $data['transfer'] = DB::connection('mysql')->select("SELECT * FROM inventory_transfer
                                                              WHERE id = $id
                                                              ORDER BY id DESC")[0];

        $data['manifest'] = json_decode($data['transfer']->data);
        $data['finalmanifest'] = json_decode($data['transfer']->finaldata);

        return View::make('inventory.transfer.view', $data);
    }

    public function inventoryTransferEdit($id)
    {
        $data['transfer'] = DB::connection('mysql')->select("SELECT * FROM inventory_transfer
                                                              WHERE id = $id
                                                              ORDER BY id DESC")[0];

        $data['manifest'] = json_decode($data['transfer']->data);

        return View::make('inventory.transfer.edit', $data);
    }

    public function inventoryTransferCategories()
    {
        $department = Input::get('id');
        $query = "SELECT ID, DepartmentID, Name, Code FROM Category";

        if ($department !== 'all') {
            $query .= " WHERE DepartmentID = $department";
        }

        $query .= " ORDER BY Name ASC";

        $json = DB::connection('mssql-squareone')->select($query);

        return Response::json($json);
    }



    public function inventoryTransferSearch()
    {
        $sku = Input::get('sku');
        $department = Input::get('department');
        $category = Input::get('category');
        $from = Input::get('from');
        $hideSP = Input::get('sp');

        $query = "SELECT ItemLookupCode, Description, Quantity FROM Item WHERE Inactive = 0 AND ";

        // SKU
        if ($sku && !$department || $sku && $department == 'all' && !$category || $sku && $department == 'all' && $category == 'all') {
            $query .= "(Description LIKE '%$sku%' OR ItemLookupCode LIKE '%$sku%')";
        }
        if ($sku && $department && $department !== 'all' && !$category) {
            $query .= "(Description LIKE '%$sku%' OR ItemLookupCode LIKE '%sku%') AND DepartmentID = $department";
        }
        if ($sku && $department == 'all' && $category && $category !== 'all') {
            $query .= "(Description LIKE '%$sku%' OR ItemLookupCode LIKE '%sku%') AND CategoryID = $category";
        }
        if ($sku && $department && $department !== 'all' && $category && $category !== 'all') {
            $query .= "(Description LIKE '%$sku%' OR ItemLookupCode LIKE '%sku%') AND DepartmentID = $department AND CategoryID = $category";
        }
        if ($sku && $department !== 'all' && $category == 'all') {
            $query .= "(Description LIKE '%$sku%' OR ItemLookupCode LIKE '%sku%') AND DepartmentID = $department";
        }

        // No SKU
        if (!$sku && $department !== 'all' && $category == 'all') {
            $query .= "DepartmentID = $department";
        }
        if (!$sku && $department == 'all' && $category !== 'all') {
            $query .= "CategoryID = $category";
        }
        if (!$sku && $department !== 'all' && $category !== 'all') {
            $query .= "DepartmentID = $department AND CategoryID = $category";
        }

        if ($hideSP) {
            $query .= " AND Description NOT LIKE 'SP %'";
        }

        $mississauga = DB::connection('mssql-squareone')->select($query);
        $toronto = DB::connection('mssql-toronto')->select($query);

        $combined = $this->combineInventoryArrays($mississauga, $toronto, $from);
        $combined = $this->suggestTransferCount($combined);

        usort($combined, "self::sortBySuggested");
        usort($combined, "self::sortByTransferCount");

        return Response::json($combined);
    }

    public function inventoryTransferSave()
    {
        $title = Input::get('title');
        $sending = Input::get('sending');
        $receiving = Input::get('receiving');
        $data = Input::get('data');

        DB::connection('mysql')->table('inventory_transfer')->insert(
            array(
                'title' => $title,
                'sending' => $sending,
                'receiving' => $receiving,
                'data' => $data
            )
        );

        return 'success';
    }

    public function inventoryTransferUpdate($id)
    {
        $data = Input::get('data');

        DB::connection('mysql')->table('inventory_transfer')->where('id', $id)->update(
            array(
                'data' => $data,
            )
        );

        return 'success';
    }

    public function inventoryTransferCancel($id)
    {

        DB::connection('mysql')->table('inventory_transfer')->where('id', $id)->update(
            array(
                'status' => 2
            )
        );

        return $this->inventoryTransferView($id);
    }

    public function inventoryTransferReopen($id)
    {

        DB::connection('mysql')->table('inventory_transfer')->where('id', $id)->update(
            array(
                'status' => 0
            )
        );

        return $this->inventoryTransferView($id);
    }

    public function inventoryTransferComplete($id)
    {
        $data = Input::get('data');

        DB::connection('mysql')->table('inventory_transfer')->where('id', $id)->update(
            array(
                'finaldata' => $data,
                'status' => 1
            )
        );

        $this->inventoryTransferStartAdjustment($id);

        return 'success';
    }

    public function inventoryTransferStartAdjustment($id)
    {
        $data['transfer'] = DB::connection('mysql')->select("SELECT * FROM inventory_transfer
                                                              WHERE id = $id
                                                              ORDER BY id DESC")[0];

        $data['manifest'] = json_decode($data['transfer']->data);

        $this->inventoryTransferAdjustQuantities($data['manifest'], $data['transfer']->sending, $data['transfer']->receiving);

        return;
    }

    public function inventoryTransferAdjustQuantities($manifest, $from, $to)
    {
        foreach ($manifest as $item) {
            $sku = $item->{'SKU'};
            $adjustment = $item->{'Qty (Tfer)'};
            $fromadjustment = "Quantity - $adjustment";
            $toadjustment = "Quantity + $adjustment";

            Queue::push('InventoryUpdateHandler', array(
                'ItemLookupCode' => $sku,
                'Location' => $from,
                'Adjustment' => $fromadjustment
            ));

            Queue::push('InventoryUpdateHandler', array(
                'ItemLookupCode' => $sku,
                'Location' => $to,
                'Adjustment' => $toadjustment
            ));
        }
    }


    private function combineInventoryArrays($m, $t, $from)
    {
        $combined = [];

        foreach ($m as $product) {
            $combined[$product->ItemLookupCode]['ItemLookupCode'] = $product->ItemLookupCode;
            $combined[$product->ItemLookupCode]['Description'] = $product->Description;

            if ($from === 'Mississauga') {
                $combined[$product->ItemLookupCode]['SendQuantity'] = $product->Quantity;

                if ($product->Quantity < 1) {
                    $combined[$product->ItemLookupCode]['SuggestedStatus'] = 'no';
                }
            } else {
                $combined[$product->ItemLookupCode]['ReceiveQuantity'] = $product->Quantity;
            }
        }

        foreach ($t as $product) {
            $combined[$product->ItemLookupCode]['ItemLookupCode'] = $product->ItemLookupCode;
            $combined[$product->ItemLookupCode]['Description'] = $product->Description;

            if ($from ==='Toronto') {
                $combined[$product->ItemLookupCode]['SendQuantity'] = $product->Quantity;

                if ($product->Quantity < 1) {
                    $combined[$product->ItemLookupCode]['SuggestedStatus'] = 'no';
                }
            } else {
                $combined[$product->ItemLookupCode]['ReceiveQuantity'] = $product->Quantity;
            }
        }

        // return $inventory;
        return $combined;
    }

    private function suggestTransferCount($combined)
    {
        foreach ($combined as $item) {
            if (isset($item['SendQuantity'])) {
                $send = $item['SendQuantity'];
            } else {
                $send = 0;
            }
            if (isset($item['ReceiveQuantity'])) {
                $receive = $item['ReceiveQuantity'];
            } else {
                $receive = 0;
            }

            if ($send > ($receive * 2) && $send != 1) {
                $recommended = floor(($send / 2) - ($receive / 2));
            } else {
                $recommended = 0;
                $combined[$item['ItemLookupCode']]['SuggestedStatus'] = 'no';
            }

            if ($recommended < 0) {
                $recommended = 0;
            }

            $combined[$item['ItemLookupCode']]['Recommended'] = $recommended;
        }

        return $combined;
    }

    // Sorters
    private static function sortBySuggested($a, $b)
    {
        if (isset($a['SuggestedStatus']) && isset($b['SuggestedStatus']))
        {
            return 0;
        }
        if (!isset($a['SuggestedStatus']) && isset($b['SuggestedStatus']))
        {
            return -1;
        }
        if (isset($a['SuggestedStatus']) && !isset($b['SuggestedStatus']))
        {
            return 1;
        }
    }

    private static function sortByTransferCount($a, $b)
    {
        if ($a['Recommended'] === $b['Recommended'])
        {
            return 0;
        }
        if ($a['Recommended'] > $b['Recommended'])
        {
            return -1;
        }
        if ($a['Recommended'] < $b['Recommended'])
        {
            return 1;
        }
    }

/**
Inventory Adjustment Log
 */

    public function inventoryAdjustmentLog()
    {
        $query = "SELECT timestamp, location, sku, `from`, `to`, `reason` FROM inventory_adjustment ORDER BY id DESC";

        $result = DB::connection('mysql')->select($query);

        $data['logs'] = $result;

        return View::make('inventory.adjustment', $data);
    }



}