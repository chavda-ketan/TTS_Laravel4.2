<?php

class InventoryUpdateHandler {

    public function fire($job, $data)
    {
        $quantity = $data['Adjustment'];
        $sku = $data['ItemLookupCode'];

        if ($data['Location'] == 'Mississauga') {
            $dbLocation = 'mssql-squareone';
        } else {
            $dbLocation = 'mssql-toronto';
        }

        DB::connection($dbLocation)->update(
            "UPDATE Item SET Quantity = $quantity WHERE ItemLookupCode = '$sku'"
        );

        $job->delete();
    }
}