<?php

class ShitController extends BaseController
{
    public function getItemData()
    {
        $query = "SELECT * FROM Item WHERE ID IN ('3133','3134', '3033','3203', '3197', '3195',  '3167', '3141', '3143', '3135', '3173','3172', '3136','3201', '3140', '3137', '3200','7601','7603','7432','7599', '7430', '7752','3168', '3170','3169' )";

        $items = DB::connection('mssql-squareone')->select($query);

        foreach ($items as $item) {
            echo "$item->ID - $item->Description - $item->ItemLookupCode <br>";
        }
        echo "<br><br> MISC <br><br>";

        $query = "SELECT * FROM Item WHERE ID IN ('3049', '3139', '3199', '3198', '3138', '3171')";

        $items = DB::connection('mssql-squareone')->select($query);

        foreach ($items as $item) {
            echo "$item->ID - $item->Description - $item->ItemLookupCode <br>";
        }

    }

    public function batchCloseWorkOrders()
    {
        $query = "SELECT * FROM [OrderManagement] WHERE Staticupdate < '2015-10-01 12:00:00:000' AND (StatusID = 14 OR StatusID = 24)";
        //$query = "SELECT * FROM [Order] WHERE Closed = 0 AND LastUpdated < '2015-01-01 12:00:00:000'";
        $orders = DB::connection('mssql-squareone')->select($query);

        foreach ($orders as $order){
            $query = "SELECT * FROM [OrderEntry] WHERE ID = $order->OrderEntryID";

            if(isset(DB::connection('mssql-squareone')->select($query)[0]->OrderID)){
                $order->ID = DB::connection('mssql-squareone')->select($query)[0]->OrderID;
            }

            echo "$order->ID <br>";
            $this->closeWorkOrder($order->ID);
        }

        return 'ok';
    }

    public function closeWorkOrder($id)
    {
        /* SELECTS */
        $query = "SELECT * FROM [Order] WHERE ID = $id";
        $order = DB::connection('mssql-squareone')->select($query)[0];

        $query = "SELECT * FROM [OrderEntry] WHERE OrderID = $id";
        $orderEntry = DB::connection('mssql-squareone')->select($query)[0];

        $itemID = $orderEntry->ItemID;

        /* UPDATES */
        $query = "UPDATE [Order] SET Closed = 1 WHERE ID = $id";
        DB::connection('mssql-squareone')->select($query);

        $query = "UPDATE [OrderEntry] SET QuantityOnOrder = 0, QuantityRTD = 1 WHERE OrderID = $id";
        DB::connection('mssql-squareone')->select($query);

        $customer = $order->CustomerID;

        /* INSERTS */

        // ShipToID,StoreID,TransactionNumber,BatchNumber,Time,                    CustomerID,CashierID,Total,SalesTax,Comment,ReferenceNumber,DBTimeStamp,        Status,ExchangeID,ChannelType,RecallID,RecallType
        // 0,       0,      33146,            17,         2016-01-19 15:18:58:000, 4,         1,        0,    0,       ,       ,               0x0000000000964B41, 0,     0,         0,          22949,   4

        $query = "INSERT INTO [Transaction]
                  (ShipToID,StoreID,BatchNumber,Time,CustomerID,CashierID,Total,SalesTax,Comment,ReferenceNumber,Status,ExchangeID,ChannelType,RecallID,RecallType)
                  VALUES (0, 0, 17, '2016-01-19 15:18:58:000', $customer, 1, 0, 0, '', '', 0, 0, 0, $id, 4)";
        DB::connection('mssql-squareone')->select($query);

        $query = "SELECT TransactionNumber FROM [dbo].[Transaction] WHERE RecallID = $id";
        $transactionID = DB::connection('mssql-squareone')->select($query)[0]->TransactionNumber;

        // ID,     StoreID,BatchNumber,Date,                   OrderID,CashierID,DeltaDeposit,TransactionNumber,Comment,DBTimeStamp
        // 45683,  0,      17,         2016-01-19 15:18:58:000,22949,  1,        0,           33146,            ,       0x0000000000964B47

        $query = "INSERT INTO [OrderHistory]
                  (StoreID,BatchNumber,Date,OrderID,CashierID,DeltaDeposit,TransactionNumber,Comment)
                  VALUES (0, 17, '2016-01-19 15:18:58:000', $id, 1, 0, $transactionID, '')";
        DB::connection('mssql-squareone')->select($query);

        // Commission,Cost,FullPrice,StoreID,ID,   TransactionNumber,ItemID,Price,PriceSource,Quantity,SalesRepID,Taxable,DetailID,Comment,DBTimeStamp,        DiscountReasonCodeID,ReturnReasonCodeID,TaxChangeReasonCodeID,SalesTax,QuantityDiscountID
        // 0,         0,   0,        0,      39652,33146,            3033,  0,    1,          1,       0,         1,      0,       ,       0x0000000000964B42, 0,                   0,                 0,                    0,       0

        $query = "INSERT INTO [TransactionEntry]
                  (Commission,Cost,FullPrice,StoreID,TransactionNumber,ItemID,Price,PriceSource,Quantity,SalesRepID,Taxable,DetailID,Comment,DiscountReasonCodeID,ReturnReasonCodeID,TaxChangeReasonCodeID,SalesTax,QuantityDiscountID)
                  VALUES (0, 0, 0, 0, $transactionID, $itemID, 0, 1, 1, 0, 1, 0, '', 0, 0, 0, 0, 0)";
        DB::connection('mssql-squareone')->select($query);

    }

    public function tonerCartridges()
    {
        $itemQuery = 'SELECT Description, ItemLookupCode, ExtendedDescription FROM Item
                      WHERE DepartmentID = 14
                      AND CategoryID = 42
                      AND Inactive != 1';

        $hp = '/hp/i';
        $samsung = '/samsung/i';
        $lexmark = '/lexmark/i';
        $canon = '/canon/i';
        $brother = '/brother/i';

        $items = DB::connection('mssql-squareone')->select($itemQuery);

        foreach ($items as $item) {
            $name = $item->Description.' Printer Toner Cartridge';
            $menu_name = $item->Description;
            $page_name = $item->Description.' Printer Toner Cartridge';
            $service = $item->Description;
            $url = str_replace(' ', '-', trim(strtolower($item->Description)));
            $url = urlencode($url);
            $menu = 1;
            $description = $item->Description.' printer toner cartridges at TechKnow Space Toronto: nr Rogers Centre & Mississauga: across from Square One';
            $keywords = 'Toner#'.$item->ItemLookupCode;

            if (preg_match($hp, $item->Description) || preg_match($hp, $item->ExtendedDescription)) {
                $parent = '449';
            }
            if (preg_match($samsung, $item->Description) || preg_match($samsung, $item->ExtendedDescription)) {
                $parent = '450';
            }
            if (preg_match($lexmark, $item->Description) || preg_match($lexmark, $item->ExtendedDescription)) {
                $parent = '448';
            }
            if (preg_match($canon, $item->Description) || preg_match($canon, $item->ExtendedDescription)) {
                $parent = '447';
            }
            if (preg_match($brother, $item->Description) || preg_match($brother, $item->ExtendedDescription)) {
                $parent = '446';
            }

            DB::connection('mysql-godaddy')->insert('INSERT INTO web_category_page (name, menu_name, page_name, service, url, parent, menu, description, keywords)
                                             VALUES (?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $url, $parent, $menu, $description, $keywords));

        }

        $ret = $items;
        return var_dump($ret);
    }


    public function battery()
    {
        $itemQuery = 'SELECT Description, ItemLookupCode, ExtendedDescription FROM Item
                      WHERE DepartmentID = 27
                      AND CategoryID = 176
                      AND Inactive != 1';

        $sony = '/sony/i';
        $samsung = '/samsung/i';
        $nokia = '/nokia/i';
        $motorola = '/motorola/i';
        $lg = '/lg/i';
        $iphone = '/iphone/i';
        $htc = '/htc/i';
        $blackberry = '/battery bb/i';

        $items = DB::connection('mssql-squareone')->select($itemQuery);

        foreach ($items as $item) {
            $name = $item->Description.' Battery';
            $menu_name = $item->Description;
            $page_name = $item->Description.' Battery';
            $service = $item->Description;
            $url = str_replace(' ', '-', trim(strtolower($item->Description)));
            $url = urlencode($url);
            $menu = 1;
            $description = $item->Description.' batteries at TechKnow Space Toronto: nr Rogers Centre & Mississauga: across from Square One';
            $keywords = 'Battery#'.$item->ItemLookupCode;

            if (preg_match($sony, $item->Description) || preg_match($sony, $item->ExtendedDescription)) {
                $parent = '405';
            }
            if (preg_match($samsung, $item->Description) || preg_match($samsung, $item->ExtendedDescription)) {
                $parent = '404';
            }
            if (preg_match($nokia, $item->Description) || preg_match($nokia, $item->ExtendedDescription)) {
                $parent = '403';
            }
            if (preg_match($motorola, $item->Description) || preg_match($motorola, $item->ExtendedDescription)) {
                $parent = '402';
            }
            if (preg_match($lg, $item->Description) || preg_match($lg, $item->ExtendedDescription)) {
                $parent = '401';
            }
            if (preg_match($iphone, $item->Description) || preg_match($iphone, $item->ExtendedDescription)) {
                $parent = '400';
            }
            if (preg_match($htc, $item->Description) || preg_match($htc, $item->ExtendedDescription)) {
                $parent = '399';
            }
            if (preg_match($blackberry, $item->Description) || preg_match($blackberry, $item->ExtendedDescription)) {
                $parent = '398';
            }

            DB::connection('mysql-godaddy')->insert('INSERT INTO web_category_page (name, menu_name, page_name, service, url, parent, menu, description, keywords)
                                             VALUES (?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $url, $parent, $menu, $description, $keywords));

        }

        $ret = $items;
        return var_dump($ret);
    }

    public function laptopBatteries()
    {
        $itemQuery = 'SELECT Description, ItemLookupCode, ExtendedDescription FROM Item
                      WHERE DepartmentID = 27
                      AND CategoryID = 90
                      AND Inactive != 1';

        $sony = '/sony/i';
        $ibmlenovo = '/(ibm|lenovo)/i';
        $acer = '/acer/i';
        $asus = '/asus/i';
        $hpcompaq = '/(hp|compaq)/i';
        $dell = '/dell/i';
        $samsung = '/samsung/i';
        $toshiba = '/toshiba/i';
        $gateway = '/gateway/i';
        $applemac = '/(apple|mac)/i';

        $items = DB::connection('mssql-squareone')->select($itemQuery);

        foreach ($items as $item) {
            $name = $item->Description.' Battery';
            $menu_name = $item->Description;
            $page_name = $item->Description.' Battery';
            $service = $item->Description;
            $url = str_replace(' ', '-', trim(strtolower($item->Description)));
            $url = urlencode($url).'/';
            $menu = 1;
            $description = $item->Description.' batteries at TechKnow Space Toronto: nr Rogers Centre & Mississauga: across from Square One';
            $keywords = 'Battery#'.$item->ItemLookupCode;

            if (preg_match($sony, $item->Description) || preg_match($sony, $item->ExtendedDescription)) {
                $parent = '521';
            }
            if (preg_match($ibmlenovo, $item->Description) || preg_match($ibmlenovo, $item->ExtendedDescription)) {
                $parent = '368';
            }
            if (preg_match($acer, $item->Description) || preg_match($acer, $item->ExtendedDescription)) {
                $parent = '362';
            }
            if (preg_match($asus, $item->Description) || preg_match($asus, $item->ExtendedDescription)) {
                $parent = '364';
            }
            if (preg_match($hpcompaq, $item->Description) || preg_match($hpcompaq, $item->ExtendedDescription)) {
                $parent = '365';
            }
            if (preg_match($dell, $item->Description) || preg_match($dell, $item->ExtendedDescription)) {
                $parent = '366';
            }
            if (preg_match($samsung, $item->Description) || preg_match($samsung, $item->ExtendedDescription)) {
                $parent = '369';
            }
            if (preg_match($toshiba, $item->Description) || preg_match($toshiba, $item->ExtendedDescription)) {
                $parent = '370';
            }
            if (preg_match($gateway, $item->Description) || preg_match($gateway, $item->ExtendedDescription)) {
                $parent = '492';
            }
            if (preg_match($applemac, $item->Description) || preg_match($applemac, $item->ExtendedDescription)) {
                $parent = '355';
            }


            DB::connection('mysql-godaddy')->insert('INSERT INTO web_category_page (name, menu_name, page_name, service, url, parent, menu, description, keywords)
                                             VALUES (?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $url, $parent, $menu, $description, $keywords));

        }

        $ret = $items;
        return var_dump($ret);
    }


    public function laptopScreenBrands()
    {

        $itemQuery = 'SELECT DISTINCT brand FROM models';

        $items = DB::connection('mysql-godaddy')->select($itemQuery);

        foreach ($items as $item) {
            $name = $item->brand.' Laptop Screen Replacement Toronto & Mississauga';
            $menu_name = $item->brand;
            $page_name = $item->brand.' Laptop Screen Replacement Toronto & Mississauga';
            $service = $item->brand.' Laptop Screen';
            $url = str_replace(' ', '-', trim(strtolower($item->brand)));
            $url = urlencode($url).'-screen';
            $parent = 503;
            $menu = 1;
            $description = $item->brand.' laptop screen replacement at TechKnow Space Toronto: nr Rogers Centre & Mississauga: across from Square One';
            $keywords = $item->brand.' - Laptop Screen Repair';


            DB::connection('mysql-godaddy')->insert('INSERT INTO web_category_page (name, menu_name, page_name, service, url, parent, menu, description, keywords)
                                             VALUES (?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $url, $parent, $menu, $description, $keywords));

        }

        $ret = $items;
        return var_dump($ret);
    }

    public function laptopScreenSeries()
    {

        $itemQuery = 'SELECT DISTINCT brand, series FROM models WHERE series != ""';

        $items = DB::connection('mysql-godaddy')->select($itemQuery);

        foreach ($items as $item) {
            $name = $item->brand.' '.$item->series.' Laptop Screen Replacement Toronto & Mississauga';
            $menu_name = $item->series;
            $page_name = $item->brand.' '.$item->series.' Laptop Screen Replacement Toronto & Mississauga';
            $service = $item->brand.' '.$item->series.' Laptop Screen';
            $url = str_replace(' ', '-', trim(strtolower($item->brand.'-'.$item->series)));
            $url = urlencode($url).'-screen';
            $menu = 1;
            $description = $item->brand.' laptop screen replacement at TechKnow Space Toronto: nr Rogers Centre & Mississauga: across from Square One';
            $keywords = $item->brand.' '.$item->series.' - Laptop Screen Repair';

            if ($item->brand == 'Apple') {
                $parent = 2729;
            }
            elseif ($item->brand == 'eMachines') {
                $parent = 2728;
            }
            elseif ($item->brand == 'Fujitsu') {
                $parent = 2727;
            }
            elseif ($item->brand == 'Lenovo') {
                $parent = 2726;
            }
            elseif ($item->brand == 'Toshiba') {
                $parent = 2725;
            }
            elseif ($item->brand == 'Sony') {
                $parent = 2724;
            }
            elseif ($item->brand == 'LG') {
                $parent = 2723;
            }
            elseif ($item->brand == 'Samsung') {
                $parent = 2722;
            }
            elseif ($item->brand == 'Gateway') {
                $parent = 2721;
            }
            elseif ($item->brand == 'HP') {
                $parent = 2720;
            }
            elseif ($item->brand == 'Dell') {
                $parent = 2719;
            }
            elseif ($item->brand == 'Asus') {
                $parent = 2718;
            }
            elseif ($item->brand == 'Acer') {
                $parent = 2717;
            }

            DB::connection('mysql-godaddy')->insert('REPLACE INTO web_category_page (name, menu_name, page_name, service, url, parent, menu, description, keywords)
                                             VALUES (?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $url, $parent, $menu, $description, $keywords));

        }

        $ret = $items;
        return var_dump($ret);
    }

    public function laptopScreens()
    {
        $itemQuery = 'SELECT DISTINCT brand, series, fullname FROM models WHERE series != ""';

        $items = DB::connection('mysql-godaddy')->select($itemQuery);

        $i = 0;

        foreach ($items as $item) {
            $i++;
            $series = $item->series;
            $name = $item->brand.' '.$item->fullname.' Laptop Screen Replacement Toronto & Mississauga';
            $menu_name = $item->fullname;
            $page_name = $item->brand.' '.$item->fullname.' Laptop Screen Replacement Toronto & Mississauga';
            $service = $item->brand.' '.$item->fullname.' Laptop Screen';
            $url = str_replace(' ', '-', trim(strtolower($item->fullname)));
            $url = urlencode($url).'-screen';
            $menu = 1;
            $description = $item->brand.' '.$item->fullname.' laptop screen replacement at TechKnow Space Toronto: nr Rogers Centre & Mississauga: across from Square One';
            $keywords = $item->brand.' '.$item->fullname.' - Laptop Screen Replacement';

            $subquery = "SELECT id FROM web_category_page WHERE menu_name = '$series'";
            $parent = DB::connection('mysql-godaddy')->select($subquery)[0]->id;

            DB::connection('mysql-godaddy')->insert('REPLACE INTO web_category_page (name, menu_name, page_name, service, url, parent, menu, description, keywords)
                                             VALUES (?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $url, $parent, $menu, $description, $keywords));

            print("$i - ".$item->fullname."\n");

            ob_flush();
            flush();
        }

        $ret = $items;
        return var_dump($ret);
    }

    // SHIT FOR SERIES-LESS SHIT
    public function laptopScreensNoSeries()
    {
        $itemQuery = 'SELECT DISTINCT brand, series FROM models WHERE series = "" AND brand != "LG" AND brand != "eMachines"';

        $items = DB::connection('mysql-godaddy')->select($itemQuery);

        $i = 0;

        foreach ($items as $item) {
            $i++;
            $name = $item->brand.' Other Series Laptop Screen Replacement Toronto & Mississauga';
            $menu_name = 'Other Series';
            $page_name = $item->brand.' '.'Other Series'.' Laptop Screen Replacement Toronto & Mississauga';
            $service = $item->brand.' '.'Other Series'.' Laptop Screen';
            $url = str_replace(' ', '-', trim(strtolower($item->brand.'-'.'Other Series')));
            $url = urlencode($url).'-screen';
            $menu = 1;
            $description = $item->brand.' laptop screen replacement at TechKnow Space Toronto: nr Rogers Centre & Mississauga: across from Square One';
            $keywords = $item->brand.' '.'Other Series'.' - Laptop Screen Repair';

            if ($item->brand == 'Apple') {
                $parent = 2729;
            }
            elseif ($item->brand == 'eMachines') {
                $parent = 2728;
            }
            elseif ($item->brand == 'Fujitsu') {
                $parent = 2727;
            }
            elseif ($item->brand == 'Lenovo') {
                $parent = 2726;
            }
            elseif ($item->brand == 'Toshiba') {
                $parent = 2725;
            }
            elseif ($item->brand == 'Sony') {
                $parent = 2724;
            }
            elseif ($item->brand == 'LG') {
                $parent = 2723;
            }
            elseif ($item->brand == 'Samsung') {
                $parent = 2722;
            }
            elseif ($item->brand == 'Gateway') {
                $parent = 2721;
            }
            elseif ($item->brand == 'HP') {
                $parent = 2720;
            }
            elseif ($item->brand == 'Dell') {
                $parent = 2719;
            }
            elseif ($item->brand == 'Asus') {
                $parent = 2718;
            }
            elseif ($item->brand == 'Acer') {
                $parent = 2717;
            }

            DB::connection('mysql-godaddy')->insert('REPLACE INTO web_category_page (name, menu_name, page_name, service, url, parent, menu, description, keywords)
                                             VALUES (?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $url, $parent, $menu, $description, $keywords));

            ob_flush();
            flush();
        }

        $ret = $items;
        return var_dump($ret);
    }

    // SERIES-LESS MODELS
    public function laptopScreensNoSeries2()
    {
        $itemQuery = 'SELECT DISTINCT brand, series, fullname FROM models WHERE series = "" AND brand != "LG" AND brand != "eMachines"';

        $items = DB::connection('mysql-godaddy')->select($itemQuery);

        $i = 0;

        foreach ($items as $item) {
            $i++;
            $series = $item->series;
            $brand = $item->brand;
            $name = $item->brand.' '.$item->fullname.' Laptop Screen Replacement Toronto & Mississauga';
            $menu_name = $item->fullname;
            $page_name = $item->brand.' '.$item->fullname.' Laptop Screen Replacement Toronto & Mississauga';
            $service = $item->brand.' '.$item->fullname.' Laptop Screen';
            $url = str_replace(' ', '-', trim(strtolower($item->fullname)));
            $url = urlencode($url).'-screen';
            $menu = 1;
            $description = $item->brand.' '.$item->fullname.' laptop screen replacement at TechKnow Space Toronto: nr Rogers Centre & Mississauga: across from Square One';
            $keywords = $item->brand.' '.$item->fullname.' - Laptop Screen Replacement';

            $subquery = "SELECT id FROM web_category_page WHERE name LIKE '$brand Other Series%'";
            $parent = DB::connection('mysql-godaddy')->select($subquery)[0]->id;

            DB::connection('mysql-godaddy')->insert('REPLACE INTO web_category_page (name, menu_name, page_name, service, url, parent, menu, description, keywords)
                                             VALUES (?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $url, $parent, $menu, $description, $keywords));

            print("$i - ".$item->fullname."\n");

            ob_flush();
            flush();
        }

        $ret = $items;
        return var_dump($ret);
    }

    public function laptopScreensNoSeries3()
    {
        $itemQuery = 'SELECT DISTINCT brand, series, fullname FROM models WHERE series = "" AND brand = "LG" OR brand = "eMachines"';

        $items = DB::connection('mysql-godaddy')->select($itemQuery);

        $i = 0;

        foreach ($items as $item) {
            $i++;
            $series = $item->series;
            $brand = $item->brand;
            $name = $item->brand.' '.$item->fullname.' Laptop Screen Replacement Toronto & Mississauga';
            $menu_name = $item->fullname;
            $page_name = $item->brand.' '.$item->fullname.' Laptop Screen Replacement Toronto & Mississauga';
            $service = $item->brand.' '.$item->fullname.' Laptop Screen';
            $url = str_replace(' ', '-', trim(strtolower($item->fullname)));
            $url = urlencode($url).'-screen';
            $menu = 1;
            $description = $item->brand.' '.$item->fullname.' laptop screen replacement at TechKnow Space Toronto: nr Rogers Centre & Mississauga: across from Square One';
            $keywords = $item->brand.' '.$item->fullname.' - Laptop Screen Replacement';

            if ($item->brand == 'eMachines') {
                $parent = 2728;
            }
            elseif ($item->brand == 'LG') {
                $parent = 2723;
            }

            DB::connection('mysql-godaddy')->insert('REPLACE INTO web_category_page (name, menu_name, page_name, service, url, parent, menu, description, keywords)
                                             VALUES (?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $url, $parent, $menu, $description, $keywords));

            print("$i - ".$item->fullname."\n");

            ob_flush();
            flush();
        }

        $ret = $items;
        return var_dump($ret);
    }


    public function batterySeries()
    {

        $itemQuery = 'SELECT DISTINCT brand, series FROM models WHERE series != ""';

        $items = DB::connection('mysql-godaddy')->select($itemQuery);

        foreach ($items as $item) {
            $name = $item->brand.' '.$item->series.' Laptop Battery Toronto & Mississauga';
            $page_name = $item->brand.' '.$item->series.' Laptop Battery Toronto & Mississauga';
            $menu_name = $item->series;
            $service = 'Battery for '.$item->series;
            $url = str_replace(' ', '-', trim(strtolower($item->brand.'-'.$item->series)));
            $url = urlencode($url).'-battery';
            $menu = 1;
            $description = $item->brand.' '.$item->series.' laptop batteries in stock - Toronto: nr Rogers Centre & Mississauga: across from Square One';
            $keywords = $item->brand.' '.$item->series.' Laptop Replacement Battery';

            if ($item->brand == 'Apple') {
                $parent = 363;
            }
            // elseif ($item->brand == 'eMachines') {
            //     $parent = 2728;
            // }
            // elseif ($item->brand == 'Fujitsu') {
            //     $parent = 2727;
            // }
            elseif ($item->brand == 'Lenovo') {
                $parent = 559;
            }
            elseif ($item->brand == 'Toshiba') {
                $parent = 370;
            }
            elseif ($item->brand == 'Sony') {
                $parent = 521;
            }
            // elseif ($item->brand == 'LG') {
            //     $parent = 2723;
            // }
            elseif ($item->brand == 'Samsung') {
                $parent = 369;
            }
            elseif ($item->brand == 'Gateway') {
                $parent = 492;
            }
            elseif ($item->brand == 'HP') {
                $parent = 491;
            }
            elseif ($item->brand == 'Dell') {
                $parent = 366;
            }
            elseif ($item->brand == 'Asus') {
                $parent = 364;
            }
            elseif ($item->brand == 'Acer') {
                $parent = 362;
            }

            DB::connection('mysql-godaddy')->insert('REPLACE INTO web_category_page (name, menu_name, page_name, service, url, parent, menu, description, keywords)
                                             VALUES (?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $url, $parent, $menu, $description, $keywords));

        }

        $ret = $items;
        return var_dump($ret);
    }

    public function batteryModels()
    {
        $itemQuery = 'SELECT DISTINCT brand, series, fullname, model FROM models WHERE series != ""';

        $items = DB::connection('mysql-godaddy')->select($itemQuery);

        $i = 0;

        foreach ($items as $item) {
            $i++;
            $series = $item->series;

            $name = $item->brand.' '.$item->fullname.' Laptop Battery Toronto & Mississauga';
            $page_name = $item->brand.' '.$item->fullname.' Laptop Battery Toronto & Mississauga';
            $menu_name = $item->model;
            $service = 'Battery for '.$item->brand;
            $model = $item->model;
            $url = str_replace(' ', '-', trim(strtolower($item->model)));
            $url = urlencode($url).'-battery';
            $menu = 1;
            $description = $item->brand.' '.$item->fullname.' laptop batteries in stock - Toronto: nr Rogers Centre & Mississauga: across from Square One';
            $keywords = $item->brand.' '.$item->fullname.' Laptop Replacement Battery';

            $subquery = "SELECT id FROM web_category_page WHERE menu_name = '$series' AND service LIKE '%Battery%'";
            $parent = DB::connection('mysql-godaddy')->select($subquery)[0]->id;

            DB::connection('mysql-godaddy')->insert('REPLACE INTO web_category_page (name, menu_name, page_name, service, url, parent, menu, description, keywords, model)
                                             VALUES (?,?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $url, $parent, $menu, $description, $keywords, $model));

            print("$i - ".$item->fullname."\n");

            ob_flush();
            flush();
        }

        $ret = $items;
        return var_dump($ret);
    }


    public function fixParents()
    {
        $itemQuery = 'SELECT * FROM web_category_page WHERE id > 211650 ORDER BY id ASC';

        $items = DB::connection('mysql-godaddy')->select($itemQuery);

        foreach ($items as $item) {
            $series = $item->menu_name;
            $id = $item->id;
            $subquery = "UPDATE web_category_page SET parent = $id WHERE menu_name LIKE '%".$series."%' AND id BETWEEN '10000' AND '211650';";
            DB::connection('mysql-godaddy')->insert($subquery);

            ob_flush();
            flush();
        }

        $ret = $items;
        return var_dump($ret);
    }

    /* LAPTOP CHARGERS */

    public function laptopChargerSeries()
    {

        $itemQuery = 'SELECT DISTINCT brand, series FROM laptop_chargers_by_laptop_model';

        $items = DB::connection('mysql-cac')->select($itemQuery);

        foreach ($items as $item) {
            if($item->series == 'none')
            {
                $titleString = $item->brand;
            } else {
                $titleString = $item->brand.' '.$item->series;
            }

            $name = $titleString.' Chargers Laptop AC Adapter Toronto & Mississauga Store Pick Up';
            $menu_name = strtoupper($titleString.' series chargers');
            $page_name = $titleString.' Chargers Laptop AC Adapter Toronto & Mississauga Store Pick Up';
            $service = 'Laptop Chargers For';
            $model = $titleString;
            $url = str_replace(' ', '-', trim(strtolower($titleString)));
            $url = urlencode($url).'-series-chargers-laptop-ac-adapters';
            $menu = 1;
            $description = "Chargers for $titleString Series laptops. Toronto: nr Rogers Centre & Mississauga: opposite Square One. $item->brand Laptop AC Adapters / Chargers in stock.";
            $keywords = ucfirst("$titleString series laptop chargers");

            if ($item->brand == 'Fujitsu') {
                $parent = 376;
            }
            elseif ($item->brand == 'Lenovo') {
                $parent = 379;
            }
            elseif ($item->brand == 'Toshiba') {
                $parent = 384;
            }
            elseif ($item->brand == 'Sony') {
                $parent = 383;
            }
            elseif ($item->brand == 'Compaq') {
                $parent = 374;
            }
            elseif ($item->brand == 'Samsung') {
                $parent = 382;
            }
            elseif ($item->brand == 'Gateway') {
                $parent = 377;
            }
            elseif ($item->brand == 'HP') {
                $parent = 374;
            }
            elseif ($item->brand == 'Dell') {
                $parent = 375;
            }
            elseif ($item->brand == 'Asus') {
                $parent = 373;
            }
            elseif ($item->brand == 'Acer') {
                $parent = 371;
            }

            DB::connection('mysql-cac')->insert('REPLACE INTO web_category_page (name, menu_name, page_name, service, model, url, parent, menu, description, keywords)
                                             VALUES (?,?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $page_name, $service, $model, $url, $parent, $menu, $description, $keywords));
        }

        $ret = $items;
        return var_dump($ret);
    }

    public function laptopChargerModels()
    {
        $itemQuery = 'SELECT DISTINCT id, brand, series, model FROM laptop_chargers_by_laptop_model';

        $items = DB::connection('mysql-cac')->select($itemQuery);

        $i = 0;

        foreach ($items as $item) {
            $i++;

            if($item->series == 'none')
            {
                $titleString = "$item->brand $item->model";
                $parentName = $item->brand;
            } else {
                $titleString = "$item->brand $item->series $item->model";
                $parentName = $item->brand.' '.$item->series;
            }

            $name = "$titleString Charger Laptop AC Adapter Toronto & Mississauga Store Pick Up";
            $menu_name = strtoupper($item->model);
            $service = "Laptop Charger for";
            $model = $titleString;
            $url = str_replace(' ', '-', trim(strtolower($titleString)));
            $url = urlencode($url).'-charger-laptop-ac-adapter';
            $menu = 1;
            $description = "Charger for $titleString. Toronto: nr Rogers Centre & Mississauga: opposite Square One. $item->brand Laptop AC Adapters / Chargers in stock.";
            $keywords = $name;
            $refid = $item->id;

            $subquery = "SELECT id FROM web_category_page WHERE model = '$parentName' AND service = 'Laptop Chargers For'";
            $parent = DB::connection('mysql-cac')->select($subquery)[0]->id;

            DB::connection('mysql-cac')->insert('REPLACE INTO web_category_page (name, menu_name, service, url, parent, refid, menu, description, keywords, model)
                                             VALUES (?,?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $service, $url, $parent, $refid, $menu, $description, $keywords, $model));

            print("$i - $titleString\n");

            ob_flush();
            flush();
        }

        $ret = $items;
        return var_dump($ret);
    }


    public function laptopChargerSubpage()
    {
        $itemQuery = 'SELECT DISTINCT id, brand, series, model, volts, amps, watts FROM laptop_chargers_by_laptop_model';

        $items = DB::connection('mysql-cac')->select($itemQuery);

        $i = 0;

        foreach ($items as $item) {
            $i++;

            if($item->series == 'none')
            {
                $titleString = "$item->brand $item->model";
                $parentName = $item->brand;
            } else {
                $titleString = "$item->brand $item->series $item->model";
                $parentName = $item->brand.' '.$item->series;
            }

            // $name = "Laptop Models Compatible with $item->volts $item->amps $item->watts for $titleString";
            $name = "Laptop Models Compatible with $item->volts $item->amps for $titleString";
            $name = str_replace('  ', ' ', $name);
            $menu_name = 'Compatible Laptop Models';
            $service = "Laptop Model Compatibility";
            $model = "charger for $titleString";
            $url = strtolower($name);
            $url = preg_replace('/\s+/', '-', $url);
            $url = str_replace('.', '-', $url);
            $url = urlencode($url);
            $menu = 1;
            $description = "$titleString $item->volts $item->amps Charger compatibility";
            $keywords = "$titleString $item->volts $item->amps Laptop Charger compatibility";

            $subquery = "SELECT id, refid FROM web_category_page WHERE model = '$titleString' AND service = 'Laptop Charger For'";
            $parent = DB::connection('mysql-cac')->select($subquery)[0]->id;
            $refid = DB::connection('mysql-cac')->select($subquery)[0]->refid;

            DB::connection('mysql-cac')->insert('REPLACE INTO web_category_page (name, menu_name, service, url, parent, refid, menu, description, keywords, model)
                                             VALUES (?,?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $service, $url, $parent, $refid, $menu, $description, $keywords, $model));

            print("$i - $url\n");

            ob_flush();
            flush();
        }

        $ret = $items;
        // return var_dump($ret);
    }

    public function printerTonerCartridges()
    {
        $itemQuery = 'SELECT DISTINCT id, brand, model FROM printers';

        $items = DB::connection('mysql-cac')->select($itemQuery);

        $i = 0;

        foreach ($items as $item) {
            $i++;

            $titleString = "$item->brand $item->model";

            $name = "$titleString Cartridge - Printer Toners In-Stock Toronto Mississauga";
            $menu_name = strtoupper($item->model);
            $service = "Printer Cartridge for";
            $model = $titleString;
            $url = str_replace(' ', '-', trim(strtolower($titleString)));
            $url = urlencode($url).'-cartridge-printer-toner';
            $menu = 1;
            $description = "Printer toner cartridges for $titleString in stock. Toronto nr Rogers Centre, Mississauga opposite Square One";
            $keywords = "Toner Cartridges for $titleString Printer";
            $refid = $item->id;
            $priority = -1;

            if ($item->brand == 'Brother') {
                $parent = 446;
            }
            elseif ($item->brand == 'Canon') {
                $parent = 447;
            }
            elseif ($item->brand == 'Lexmark') {
                $parent = 448;
            }
            elseif ($item->brand == 'HP') {
                $parent = 449;
            }
            elseif ($item->brand == 'Samsung') {
                $parent = 450;
            }

            DB::connection('mysql-cac')->insert('REPLACE INTO web_category_page (name, menu_name, service, url, parent, refid, menu, description, keywords, model, priority)
                                             VALUES (?,?,?,?,?,?,?,?,?,?,?)', array($name, $menu_name, $service, $url, $parent, $refid, $menu, $description, $keywords, $model, $priority));

            print("$i - $titleString\n");

            ob_flush();
            flush();
        }

        $ret = $items;
        // return var_dump($ret);
    }


    /* binding for shit on the shitty shit */

    public function toggleTrack()
    {
        $id = Input::get('id');

        DB::connection('mysql')->update('UPDATE campaign_group SET track = NOT track
                                         WHERE id = ?', array($id));

        return 'success';
    }

    /* turds */
    public function rangeAdd()
    {
        $poop = Input::get('id');
        $start = Input::get('start');
        $end = Input::get('end');

        if (!$end) {
            $end = '0000-00-00';
        }

        DB::connection('mysql')->insert('INSERT INTO campaign_group_type_custom (group_id, d_f, d_t)
                                         VALUES (?, ?, ?)', array($poop, $start, $end));

        return 'success';
    }

    /* flush */
    public function rangeDel()
    {
        $diarrhea = Input::get('id');

        DB::connection('mysql')->delete('DELETE FROM campaign_group_type_custom
                                         WHERE id = ?', array($diarrhea));

        return 'success';
    }

    /* weewee poopoo mudbutt */
    public function showRangeDailyData()
    {
        $id = Input::get('id');
        $rms = Input::get('rms');
        $start = Input::get('start');
        $end = Input::get('end');

        $startDateTime = new DateTime( $start );
        $endDateTime = new DateTime( $end );
        $endDateTime = $endDateTime->modify( '+1 day' );

        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDateTime, $interval, $endDateTime);

        $data = $data['dates'] = array();

        foreach($dateRange as $date){
            $day = $date->format("Y-m-d");
            $data['dates'][$day] = $this->getAdwordsData($day, $day, $rms, $id);
        }

        $data['dates'] = array_reverse($data['dates']);
        $data['class'] = str_replace('-','', $start.$end);

        return View::make('snippets.adwordsrange', $data);
        // return var_dump($data);
    }

    protected function getAdwordsData($date_f,$date_t,$rms,$group)
    {
        $campaigns = '';

        $campaignResult = DB::connection('mysql')->select("SELECT group_id, campaign_id
                                         FROM campaign_group_type INNER JOIN campaign_group_item
                                         ON campaign_group_type.id = campaign_group_item.type
                                         WHERE campaign_group_type.group_id = ?", array($group));

        foreach($campaignResult as $row)
        {
            $id = $row->campaign_id;
            $campaigns .= "$id,";
        }

        $campaigns = rtrim($campaigns, ',');

        // RMS Data
        $query = "SELECT (sum(wo)+sum(wo_a)) AS wo,
                (sum(sale)+sum(sale_a)) as sale from adword_r_c_p_rms
                where name='$rms' and date between '$date_f' and '$date_t'";

        $rms = DB::connection('mysql')->select($query)[0];

        // Adwords Data
        $adwordsQuery = "SELECT sum(cost) as cost,
                        (sum(cost)/sum(clicks)/1000000) as cpc,
                        (sum(clicks)/sum(impressions)*100) as ctr,
                        sum(clicks) as clicks, avg(avgPosition) as pos,
                        sum(impressions) imp, avg(qualityScore) as qs
                        FROM adword_keyword where campaignID in ($campaigns)";

        $adwordsQueryFragment = "and device!=2 and date between '$date_f' and '$date_t'";
        $adwordsMobileQueryFragment = "and device=2 and date between '$date_f' and '$date_t'";

        $adword = DB::connection('mysql')->select($adwordsQuery.$adwordsQueryFragment)[0];
        $adword_m = DB::connection('mysql')->select($adwordsQuery.$adwordsMobileQueryFragment)[0];

        $date1 = new DateTime($date_f);
        $date2 = new DateTime($date_t);

        // $dd=$date2->diff($date1)->format("%a");
        // $dd=$dd+1;
        $dd = 1;

        $adwordsData = array();

        // All
        $adwordsData['pos'] = number_format($adword->pos, 1);
        $adwordsData['clicks'] = number_format($adword->clicks / $dd, 2);
        $adwordsData['ctr'] = number_format($adword->ctr, 2);
        $adwordsData['cpc'] = number_format($adword->cpc, 2);
        $adwordsData['qs'] = number_format($adword->qs, 1);
        $adwordsData['imp'] = ($adword->imp / $dd);
        $adwordsData['cost'] = ($adword->cost / $dd / 1000000);

        // Mobile
        $adwordsData['pos_m'] = number_format($adword_m->pos, 1);
        $adwordsData['clicks_m'] = number_format($adword_m->clicks/$dd, 2);
        $adwordsData['ctr_m'] = number_format($adword_m->ctr, 2);
        $adwordsData['cpc_m'] = number_format($adword_m->cpc, 2);
        $adwordsData['qs_m'] = number_format($adword_m->qs, 1);
        $adwordsData['imp_m'] = ($adword_m->imp / $dd);
        $adwordsData['cost_m'] = ($adword_m->cost / $dd / 1000000);

        $adwordsData['rms_wo'] = number_format($rms->wo / $dd, 2);
        $adwordsData['rms_sale'] = ($rms->sale / $dd);

        $adwordsData['campaigns'] = $campaigns;

        return $adwordsData;
    }

    public function divideAndFormat($formula)
    {


        return $answer;
    }
}
