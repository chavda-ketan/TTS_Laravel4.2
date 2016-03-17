 <?php

class AccountController extends BaseController
{
    public function businessAccountForm()
    {
        return View::make('records.addcustomer');
    }

    public function addBusinessAccount()
    {
        $customerData = Input::get();

        // var_dump($customerData);
        $this->insertAccount('mssql-toronto', $customerData);
        $this->insertAccount('mssql-squareone', $customerData);

        return Redirect::to('/b2b/add');
    }

    protected function insertAccount($database, $customerData)
    {

        DB::connection($database)->table('Customer')->insert(
            array(
                'AccountNumber' => $customerData['Phone'],

                'Company' => $customerData['Company'],
                'Address' => $customerData['Address'],
                'City' => $customerData['City'],
                'Zip' => $customerData['Postal'],

                'PhoneNumber' => $customerData['Phone'],
                'EmailAddress' => $customerData['Email'],
                'Address2' => $customerData['Email'],

                'CustomText1' => $customerData['CustomText1'],
                'CustomText2' => $customerData['CustomText2'],
                'Email2' => $customerData['Email2'],

                'CreditLimit' => $customerData['CreditLimit'],

                'FirstName' => 'Accounts',
                'LastName' => 'Payable',
                'State' => 'Ontario',
                'Country' => 'Canada',

                'AccountTypeID' => 0,
                'AssessFinanceCharges' => 1
            )
        );
    }

    public function getBusinessWorkOrders()
    {
        $company = Input::get('company');
        $query = "SELECT * FROM Customer, [Order], OrderEntry WHERE CustomerID = Customer.ID AND Company = '$company' AND OrderID = [Order].ID";
        $toronto = DB::connection('mssql-toronto')->select($query);
        $squareone = DB::connection('mssql-squareone')->select($query);

        $data['workorders']['toronto'] = $toronto;
        $data['workorders']['squareone'] = $squareone;

        return View::make('records.workorders', $data);
    }
}
