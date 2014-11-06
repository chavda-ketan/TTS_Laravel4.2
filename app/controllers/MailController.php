<?php

class MailController extends BaseController
{

    /* Happy Customer mail log table */
    public function index()
    {
        $null = '0000-00-00 00:00:00';
        $customers = DB::connection('mysql-godaddy')->select("SELECT * FROM thank_you_email WHERE email_open_date != '$null' ORDER BY email_open_date DESC, google_click_date DESC");

        foreach ($customers as $customer) {
            if ($customer->yelp_click_date != $null || $customer->google_click_date != $null) {
                $customerNameQuery = "SELECT OrderID, Customer.FirstName AS firstName, Customer.LastName AS lastName,
		            Customer.EmailAddress AS emailAddress FROM [Order], Customer, OrderEntry
		            WHERE Customer.ID=[Order].CustomerID AND OrderEntry.OrderID=[Order].ID AND OrderID = $customer->wo_id";

                $customer->class = 'success';

                if ($customer->s == 't') {
                    $name = DB::connection('mssql-toronto')->select($customerNameQuery)[0];

                    $customer->firstName = $name->firstName;
                    $customer->lastName = $name->lastName;
                    $customer->emailAddress = $name->emailAddress;
                } else {
                    $name = DB::connection('mssql-squareone')->select($customerNameQuery)[0];

                    $customer->firstName = $name->firstName;
                    $customer->lastName = $name->lastName;
                    $customer->emailAddress = $name->emailAddress;
                }
            }
        }

        $data['customers'] = $customers;

        return View::make('reports.maillog', $data);
    }

    protected function addCustomerDetailsToRow($customer, $name)
    {
        $customer->firstName = $name->firstName;
        $customer->lastName = $name->lastName;
        $customer->emailAddress = $name->emailAddress;
    }

    /* test the email template */
    public function testEmail()
    {
        // $this->sendThankYouEmail('Aubrey','acottle@taimalabs.com','iPhone','m',rand(99999,999999));
        $this->sendThankYouEmail('Jason', 'connectedideas@hotmail.com', 'iPhone', 'm', rand(99999, 999999));
        return 'Sent';
    }

    /* Iterate over customers from the previous business day for thankyou mails */
    public function thankCustomers()
    {
        $serviceList = $this->staticServiceList();
        $customerList = $this->loadCustomersToThank();

        foreach ($customerList as $customer) {
            if (isset($serviceList[$customer->ItemID])) {

                /* Validate email address */
                $validator = Validator::make(
                    array('email' => $customer->EmailAddress),
                    array('email' => 'required|email')
                );

                if ($validator->passes()) {
                    /* Check if the applicable workorder already had an email sent, skip if so */
                    $select = DB::connection('mysql-godaddy')->table('thank_you_email')->where('wo_id', '=', $customer->OrderID)->first();

                    if (is_null($select)) {
                        $serviceRendered = $serviceList[$customer->ItemID];

                        print($customer->EmailAddress . ' - ');
                        print($customer->FirstName . ' - ');
                        print($customer->OrderID . ' - ');
                        print($customer->Location . ' - ');

                        print($serviceRendered);
                        print('<br>');

                        $this->sendThankYouEmail($customer->FirstName, $customer->EmailAddress, $serviceRendered, $customer->Location, $customer->OrderID);
                    }
                }
            }

        }
    }

    /* Send the email template */
    private function sendThankYouEmail($name, $email, $service, $location, $wo_id)
    {
        /* Check if the applicable workorder already had an email sent, skip if so */
        // $query = "SELECT * FROM thank_you_email WHERE wo_id = '$wo_id'";
        $select = DB::connection('mysql-godaddy')->table('thank_you_email')->where('wo_id', '=', $wo_id)->first();

        if (is_null($select)) {

            $query = "INSERT INTO thank_you_email SET wo_id='$wo_id', s='$location', sent_date=NOW()";
            $insert = DB::connection('mysql-godaddy')->insert($query);

            // Recipient data
            $user = array(
                'email' => $email,
                'name' => $name,
            );

            // the data that will be passed into the mail view blade template
            $data = array(
                'name' => $user['name'],
                'service' => $service,
                'location' => $location,
                'wo_id' => $wo_id,
            );

            // Queue up the mail so things don't hang. Yay for Redis
            Mail::queue('emails.thankyou', $data, function ($message) use ($user) {
                $message->from('service@techknowspace.com', 'TechKnow Space');
                $message->to($user['email'], $user['name'])->subject('Thank You!');
            });

        }
    }

    public function listThankedCustomers()
    {
        $serviceList = $this->staticServiceList();
        $customerList = $this->loadCustomersToThank();

        foreach ($customerList as $customer) {
            if (isset($serviceList[$customer->ItemID])) {

                /* Validate email address */
                $validator = Validator::make(
                    array('email' => $customer->EmailAddress),
                    array('email' => 'required|email')
                );

                if ($validator->passes()) {
                    $serviceRendered = $serviceList[$customer->ItemID];

                    print($customer->EmailAddress . ' - ');
                    print($customer->FirstName . ' - ');
                    print($customer->OrderID . ' - ');
                    print($customer->Location . ' - ');

                    print($serviceRendered);
                    print('<br>');

                }
            }
        }
    }

    /* Pull yesterday's customers from Toronto/SquareOne dbs */
    private function loadCustomersToThank()
    {
        // $date = date("Y-m-d", strtotime("-150 days"));
        $date = date("Y-m-d", strtotime("-30 days"));
        $date .= ' 00:00:00';
        // $date2 = date("Y-m-d", strtotime("-1 days"));
        $date2 = date("Y-m-d", strtotime("+2"));
        $date2 .= ' 00:00:00';

        $sql = "SELECT OrderID, ItemID, OrderEntry.\"Comment\", Customer.FirstName AS FirstName,
            Customer.EmailAddress AS EmailAddress FROM OrderEntry, \"Order\",
            Customer WHERE \"Order\".Closed=1 AND \"Order\".Total NOT IN ('28.25','45.20')
            AND \"Order\".Total>0 AND Customer.ID=\"Order\".CustomerID
            AND OrderEntry.OrderID=\"Order\".ID AND OrderEntry.Price > 0 AND OrderEntry.LastUpdated > '$date'
            AND OrderEntry.LastUpdated < '$date2' AND OrderEntry.\"Comment\" LIKE '%YYY%'
            AND DATALENGTH(Customer.EmailAddress) > 0
            ORDER BY OrderEntry.ID DESC";

        $users = DB::connection('mssql-squareone')->select($sql);
        $users2 = DB::connection('mssql-toronto')->select($sql);

        // add the location info to each customer
        foreach ($users as $user) {
            $user->Location = 'm';
        }

        foreach ($users2 as $user) {
            $user->Location = 't';
        }

        $users = array_merge($users, $users2);

        return $users;
    }

    /* Service list from RMS */
    private function loadServiceList()
    {
        $sql = "SELECT id, Description FROM item";

        $services = DB::connection('mssql-squareone')->select($sql);
        $services2 = DB::connection('mssql-toronto')->select($sql);

        $services = array_merge($services, $services2);

        $item = array();

        foreach ($services as $service) {
            $item[$service->id] = $service->Description;
        }

        return $item;
    }

    private function staticServiceList()
    {
        $item[3033] = "iPhone";
        $item[3195] = "iPad";
        $item[3168] = "Laptop";
        $item[3167] = "Laptop Screen";
        $item[3140] = "Samsung";
        $item[3133] = "Blackberry";
        $item[3141] = "LG";
        $item[3173] = "Macbook";
        $item[3201] = "PS3";
        $item[3136] = "Nokia";
        $item[7432] = "iPad Mini";
        $item[3134] = "HTC";
        $item[3202] = "iPod Touch";
        $item[7430] = "Asus Tab";
        $item[3137] = "Sony";
        $item[3172] = "Macbook Screen";
        $item[7603] = "Virus Removal";
        $item[7601] = "Amazon Kindle";
        $item[3135] = "Motorola";
        $item[3200] = "Xbox";
        $item[3197] = "iPod";
        $item[7599] = "Microsoft Surface";
        $item[7752] = "Liquid Damage";

        return $item;
    }

    /* SMS mail */
    public function sendSMSEmail()
    {
        $wo = Input::get('wo');
        $sales = Input::get('s');

        $rounded = round($sales, 2);

        $content = "WO #$wo - $$rounded";

        $data = array(
            'content' => $content,
        );

        Mail::send(array('text' => 'emails.sms'), $data, function ($message) use ($content) {
            $message->from('t@techknowspace.com', 'TTS');
            $message->to('6472027359@msg.telus.com', 'Ash')->subject('WO');
        });

        return $content;
    }
}
