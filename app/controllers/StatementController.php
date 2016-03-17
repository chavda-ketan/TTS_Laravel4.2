<?php

class StatementController extends BaseController {

    public function jasonTest()
    {
        return $this->generateReceipt('mssql-squareone', 4, 31518);
    }

    public function outstandingBalances()
    {
        $data['toronto'] = $this->getCreditCustomers('mssql-toronto');
        $data['mississauga'] = $this->getCreditCustomers('mssql-squareone');

        return View::make('records.outstandingbalances', $data);
    }

    public function creditCustomers()
    {
        $toronto = $this->getCreditCustomers('mssql-toronto');
        $mississauga = $this->getCreditCustomers('mssql-squareone');

        $creditCustomers = [];

        foreach ($toronto as $customer) {

            $creditCustomers[$customer->EmailAddress] = $customer;

        }

        foreach ($mississauga as $customer) {

            if (isset($creditCustomers[$customer->EmailAddress])) {
                // $creditCustomers[$customer->EmailAddress] = (object) array_merge((array) $customer, (array) $creditCustomers[$customer->EmailAddress]);
                // $creditCustomers[$customer->EmailAddress] = (object) array_merge((array) $creditCustomers[$customer->EmailAddress], (array) $customer);

                $creditCustomers[$customer->EmailAddress]->CreditLimit =
                    $creditCustomers[$customer->EmailAddress]->CreditLimit + $customer->CreditLimit;
                $creditCustomers[$customer->EmailAddress]->AccountBalance =
                    $creditCustomers[$customer->EmailAddress]->AccountBalance + $customer->AccountBalance;
                $creditCustomers[$customer->EmailAddress]->TotalSales = $creditCustomers[$customer->EmailAddress]->TotalSales + $customer->TotalSales;

            } else {

                $creditCustomers[$customer->EmailAddress] = $customer;

            }

        }

        $data['customers'] = $creditCustomers;

        return View::make('records.creditcustomers', $data);
    }

    public function creditCustomerEditList()
    {
        $data['toronto'] = $this->getCreditCustomers('mssql-toronto');
        $data['mississauga'] = $this->getCreditCustomers('mssql-squareone');

        return View::make('records.customereditlist', $data);
    }

    public function editCreditCustomer($location, $id)
    {
        $data['customer'] = $this->getCustomer($location, $id);

        return View::make('records.edit', $data);
    }

    public function saveCreditCustomer($location, $id)
    {
        $customText1 = Input::get('CustomText1');
        $customText2 = Input::get('CustomText2');
        $email2 = Input::get('Email2');

        DB::connection($location)->update("UPDATE Customer SET CustomText1 = '$customText1', CustomText2 = '$customText2', Email2 = '$email2' WHERE ID = $id");

        return Redirect::to('/statement/balances');
    }

    public function sendReceiptForm()
    {
        return View::make('records.sendinvoice');
    }

    public function sendSingleReceipt()
    {
        $customer = Input::get('customer');
        $location = Input::get('location');
        $transaction = Input::get('transaction');

        $query = "SELECT ID, EmailAddress, CustomText3 AS OtherEmail FROM Customer WHERE Company = '$customer'";
        $customerID = DB::connection("mssql-$location")->select($query);

        $rendered = $this->generateReceipt("mssql-$location", $customerID[0]->ID, $transaction);
        $receipt['company'] = $customer;
        $receipt['receipts'][] = $rendered;

        $this->mailReceipts($receipt, $customerID[0]->EmailAddress);

        return Redirect::to('/statement/send');
    }

    public function customerReceiptCron()
    {
        $query = "SELECT * FROM Customer, [Transaction]
                  WHERE Customer.CreditLimit = 0
                  AND [Transaction].CustomerID = Customer.ID
                  AND [Transaction].Time >= dateadd(day,datediff(day,1,GETDATE()),0)
                  AND [Transaction].Time < dateadd(day,datediff(day,0,GETDATE()),0)";

        $customersS1 = DB::connection('mssql-squareone')->select($query);
        $customersTO = DB::connection('mssql-toronto')->select($query);

        $receipts = [];

        foreach ($customersS1 as $customer) {
            if (isset($receipt['receipts'])) {
                unset($receipt['receipts']);
            }

            $rendered = $this->generateReceipt('mssql-squareone', $customer->ID, $customer->TransactionNumber);
            $receipt['receipts'][] = $rendered;

            $this->mailReceiptsCustomer($receipt, $customer->EmailAddress);
        }

        foreach ($customersTO as $customer) {
            unset($receipt['receipts']);
            $rendered = $this->generateReceipt('mssql-toronto', $customer->ID, $customer->TransactionNumber);
            $receipt['receipts'][] = $rendered;

            $this->mailReceiptsCustomer($receipt, $customer->EmailAddress);
        }

        return 'ok';
        // return var_dump($customersS1);
    }

    public function accountReceiptCron()
    {
        $query = "SELECT * FROM Customer, [Transaction]
                  WHERE Customer.CreditLimit > 0
                  AND [Transaction].CustomerID = Customer.ID
                  AND [Transaction].Time >= dateadd(day,datediff(day,1,GETDATE()),0)
                  AND [Transaction].Time < dateadd(day,datediff(day,0,GETDATE()),0)
                  AND Company != 'Samsung Electronics Canada Inc'";

        $customersS1 = DB::connection('mssql-squareone')->select($query);
        $customersTO = DB::connection('mssql-toronto')->select($query);

        $receipts = [];

        foreach ($customersS1 as $customer) {
            if (isset($receipt['receipts'])) {
                unset($receipt['receipts']);
            }

            $rendered = $this->generateReceipt('mssql-squareone', $customer->ID, $customer->TransactionNumber);
            $receipt['company'] = $customer->Company;
            $receipt['receipts'][] = $rendered;

            if (isset($customer->Email2)) {
                $receipt['cc'] = $customer->Email2;
            }

            // $this->mailReceipts($receipt, $customer->EmailAddress);
        }

        foreach ($customersTO as $customer) {
            unset($receipt['receipts']);
            $rendered = $this->generateReceipt('mssql-toronto', $customer->ID, $customer->TransactionNumber);
            $receipt['company'] = $customer->Company;
            $receipt['receipts'][] = $rendered;

            if (isset($customer->Email2)) {
                $receipt['cc'] = $customer->Email2;
            }

            // $this->mailReceipts($receipt, $customer->EmailAddress);
        }

        return 'ok';
    }

    public function batchSendInvoices()
    {
        $begin = new DateTime('2015-11-01 00:00:00');
        $end = new DateTime('2015-11-30 00:00:00');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $day) {
            $date = $day->format("Y-m-d 00:00:00:000");
            $receipts = $this->getReceiptsForDay($date);

            $this->processReceiptsFake($receipts);
        }

        return 'ok';
    }

    public function getReceiptsForDay($day)
    {
        $query = "SELECT * FROM Customer, [Transaction]
                  WHERE Customer.CreditLimit > 0
                  AND [Transaction].CustomerID = Customer.ID
                  AND [Transaction].Time >= dateadd(day,datediff(day,1,'$day'),0)
                  AND [Transaction].Time < dateadd(day,datediff(day,0,'$day'),0)
                  AND Company = 'Johnston Equipment'";

        $return['customersS1'] = DB::connection('mssql-squareone')->select($query);
        $return['customersTO'] = DB::connection('mssql-toronto')->select($query);

        return $return;
    }

    public function processReceipts($customers)
    {
        $receipts = [];

        foreach ($customers['customersS1'] as $customer) {
            $rendered = $this->generateReceipt('mssql-squareone', $customer->ID, $customer->TransactionNumber);
            $receipts[$customer->EmailAddress]['company'] = $customer->Company;
            $receipts[$customer->EmailAddress]['receipts'][] = $rendered;


            if (isset($customer->Email2)) {
                $receipts[$customer->EmailAddress]['cc'] = $customer->Email2;
            }
        }

        foreach ($customers['customersTO'] as $customer) {
            $rendered = $this->generateReceipt('mssql-toronto', $customer->ID, $customer->TransactionNumber);
            $receipts[$customer->EmailAddress]['company'] = $customer->Company;
            $receipts[$customer->EmailAddress]['receipts'][] = $rendered;

            if (isset($customer->Email2)) {
                $receipts[$customer->EmailAddress]['cc'] = $customer->Email2;
            }
        }

        foreach ($receipts as $email => $receipt) {
            $this->mailReceipts($receipt, $email);
        }

        return;
    }

    public function generateReceipt($database, $customerId, $transactionNumber)
    {
        $customer = $this->getCustomer($database, $customerId);
        $transaction = $this->getTransactionDate($database, $transactionNumber);
        $tender = $this->getTender($database, $transactionNumber);
        $lineItemsForTransaction = $this->getTransactionLineItems($database, $transactionNumber);
        $orderEntryDescriptions = $this->getOrderEntryDescriptions($database, $transactionNumber);

        if (isset($orderEntryDescriptions[0])) {
            $order = $this->getOrder($database, $orderEntryDescriptions[0]->OrderID);
            $data['orderEntry'] = $orderEntryDescriptions;
            $data['order'] = $order;
        }

        if (isset($lineItemsForTransaction[0])) {
            $data['lineItems'] = $lineItemsForTransaction;
        }

        // edge case where a business account only purchased retail items in a transaction
        if (isset($lineItemsForTransaction[0]) && !isset($orderEntryDescriptions[0])) {
            $total = 0;

            foreach($lineItemsForTransaction as $lineItem) {
                $total = $total + $lineItem->Price;
            }

            $tax = $total * 1.13 - $total;

            $data['order'] = new stdClass();
            $data['order']->Total = $total + $tax;
            $data['order']->Tax = $tax;
        }

        $data['transaction'] = $transaction;
        $data['transactionNumber'] = $transactionNumber;
        $data['customerId'] = $customerId;
        $data['account'] = $customer;
        $data['tender'] = $tender;

        $storagePath = storage_path().'/';
        $fileName = "$customer->EmailAddress-$transactionNumber-receipt.pdf";
        $fullReceiptPath = $storagePath.$fileName;

        PDF::loadView('emails.statement.receipt-pdf', $data)->save($fullReceiptPath);

        $view = View::make('emails.statement.receipt', $data);
        $receipt = (string) $view;

        return $receipt;
    }

    public function mailReceiptsCustomer($receipts, $email)
    {
        $data['receipts'] = $receipts['receipts'];

        $storagePath = storage_path().'/';
        $fileName = $email.'-receipt.pdf';
        $fullReceiptPath = $storagePath.$fileName;

        $mailto = array(
            'email' => $email,
            'attachment' => $fullReceiptPath
        );

        Mail::send('emails.statement.combinedreceiptcustomer', $data, function ($message) use ($mailto) {
            $message->from('service@techknowspace.com', 'The TechKnow Space');
            $message->to($mailto['email'])->subject('Our Receipt Attached - just in case you need it!');
            $message->attach($mailto['attachment']);
        });
    }


    public function mailReceipts($receipts, $email)
    {
        $data['receipts'] = $receipts['receipts'];

        $storagePath = storage_path().'/';
        $fileName = $email.'-receipt.pdf';
        $fullReceiptPath = $storagePath.$fileName;

        $mailto = array(
            'email' => $email,
            'attachment' => $fullReceiptPath,
            'company' => $receipts['company']
        );

        if (isset($receipts['cc'])) {
            $mailto['cc'] = $receipts['cc'];
        }

        Mail::send('emails.statement.combinedreceipt', $data, function ($message) use ($mailto) {
            $message->from('service@techknowspace.com', 'The TechKnow Space');
            $message->to($mailto['email'])->subject('Your Invoice - '.$mailto['company']);

            if (isset($mailto['cc']) && $mailto['cc'] != $mailto['email']) {
                $message->cc(['b2badmin@techknowspace.com',$mailto['cc']]);
            } else {
                $message->cc('b2badmin@techknowspace.com');
            }

            $message->attach($mailto['attachment']);
        });
    }

    public function sendStatementForm()
    {
        return View::make('records.sendstatement');
    }

    public function sendSingleStatement()
    {
        $customer = Input::get('customer');

        $query = "SELECT * FROM Customer WHERE Company = '$customer'";

        $customersS1 = DB::connection('mssql-squareone')->select($query);
        $customersTO = DB::connection('mssql-toronto')->select($query);

        $statements = [];

        foreach ($customersS1 as $customer) {
            $rendered = $this->generateStatement('mssql-squareone', $customer->ID);
            $statements[$customer->EmailAddress]['pdf'][] = $rendered[2];
            $statements[$customer->EmailAddress]['name'] = $customer->Company;

            $statements[$customer->EmailAddress]['balance'] = $rendered[1];
            if (isset($customer->Email2)) {
                $statements[$customer->EmailAddress]['cc'] = $customer->Email2;
            }

            $statements[$customer->EmailAddress]['transactionNumbers'] = $rendered[3];
        }

        foreach ($customersTO as $customer) {
            $rendered = $this->generateStatement('mssql-toronto', $customer->ID);
            $statements[$customer->EmailAddress]['pdf'][] = $rendered[2];
            $statements[$customer->EmailAddress]['name'] = $customer->Company;

            if (isset($statements[$customer->EmailAddress]['balance'])) {
                $statements[$customer->EmailAddress]['balance'] = $statements[$customer->EmailAddress]['balance'] + $rendered[1];
            } else {
                $statements[$customer->EmailAddress]['balance'] = $rendered[1];
            }

            if (isset($customer->Email2)) {
                $statements[$customer->EmailAddress]['cc'] = $customer->Email2;
            }

            if (isset($statements[$customer->EmailAddress]['transactionNumbers'][0])) {
                $statements[$customer->EmailAddress]['transactionNumbers'] = array_merge($statements[$customer->EmailAddress]['transactionNumbers'], $rendered[3]);
            } else {
                $statements[$customer->EmailAddress]['transactionNumbers'] = $rendered[3];
            }
        }

        foreach ($statements as $email => $statement) {
            $this->sendConsolidatedStatements($email, $statement, 1);
        }

        return 'ok';

        return Redirect::to('/statement/send2');
    }

    public function accountStatementCron()
    {
        //$query = "SELECT * FROM Customer WHERE CreditLimit > 0 AND Company != 'StatPro Canada'";
        $query = "SELECT * FROM Customer WHERE AccountBalance > 0 AND Company != 'W3 Solutions'";
        // $query = "SELECT * FROM Customer WHERE AccountBalance > 0 AND Company = 'Workopolis'";

        $customersS1 = DB::connection('mssql-squareone')->select($query);
        $customersTO = DB::connection('mssql-toronto')->select($query);

        $statements = [];

        foreach ($customersS1 as $customer) {
            $rendered = $this->generateStatement('mssql-squareone', $customer->ID);
            // $statements[$customer->EmailAddress]['statement'][] = $rendered[0];
            $statements[$customer->EmailAddress]['pdf'][] = $rendered[2];
            $statements[$customer->EmailAddress]['name'] = $customer->Company;

            $statements[$customer->EmailAddress]['balance'] = $rendered[1];

            if (isset($customer->Email2)) {
                $statements[$customer->EmailAddress]['cc'] = $customer->Email2;
            }

            $statements[$customer->EmailAddress]['transactionNumbers'] = $rendered[3];
        }

        foreach ($customersTO as $customer) {
            $rendered = $this->generateStatement('mssql-toronto', $customer->ID);
            // $statements[$customer->EmailAddress]['statement'][] = $rendered[0];
            $statements[$customer->EmailAddress]['pdf'][] = $rendered[2];
            $statements[$customer->EmailAddress]['name'] = $customer->Company;

            if (isset($statements[$customer->EmailAddress]['balance'])) {
                $statements[$customer->EmailAddress]['balance'] = $statements[$customer->EmailAddress]['balance'] + $rendered[1];
            } else {
                $statements[$customer->EmailAddress]['balance'] = $rendered[1];
            }

            if (isset($customer->Email2)) {
                $statements[$customer->EmailAddress]['cc'] = $customer->Email2;
            }

            if (isset($statements[$customer->EmailAddress]['transactionNumbers'][0])) {
                $statements[$customer->EmailAddress]['transactionNumbers'] = array_merge($statements[$customer->EmailAddress]['transactionNumbers'], $rendered[3]);
            } else {
                $statements[$customer->EmailAddress]['transactionNumbers'] = $rendered[3];
            }
        }

        foreach ($statements as $email => $statement) {
           $this->sendConsolidatedStatements($email, $statement, 0);
        }

        return 'ok';
    }

    public function sendConsolidatedStatements($email, $statements, $manual = false)
    {
        $data['balance'] = $statements['balance'];
        $data['pdf'] = $statements['pdf'];
        $data['month'] = date('F', strtotime('-1 month'));
        $transactions = $statements['transactionNumbers'];

        $storagePath = storage_path().'/';
        $fileName = $email.'-statement.pdf';
        $fullInvoicePath = $storagePath.$fileName;

        PDF::loadView('emails.statement.combinedstatement', $data)->save($fullInvoicePath);

        $attachments[] = $fullInvoicePath;

        foreach ($transactions as $transaction) {
            $fileName = "$email-$transaction-receipt.pdf";
            $fullReceiptPath = $storagePath.$fileName;

            $attachments[] = $fullReceiptPath;
        }

        $mailto = array(
            'email' => $email,
            'attachments' => $attachments,
            'name' => $statements['name']
        );

        if (isset($statements['cc'])) {
            $mailto['cc'] = $statements['cc'];
        }

        if ($manual) {
            $mailto['email'] = 'acottle@taimalabs.com';
            $mailto['cc'] = 'acottle@taimalabs.com';
        }

        Mail::send('emails.statement.finalstatement', $data, function ($message) use ($mailto) {
            $message->from('service@mg.techknowspace.com', 'Accounts Receivable - The TechKnow Space');
            $message->to($mailto['email'])->subject('Account Statement - '.$mailto['name']);
            $message->cc('b2badmin@techknowspace.com');

            if (isset($mailto['cc']) && $mailto['cc'] != $mailto['email']) {
                $message->cc(['b2badmin@techknowspace.com',$mailto['cc']]);
            } else {
                $message->cc('b2badmin@techknowspace.com');
            }

            //$message->to('acottle@taimalabs.com')->subject('Account Statement - '.$mailto['name']);

            $size = sizeOf($mailto['attachments']);
            for($i=0; $i < $size; $i++){
                $message->attach($mailto['attachments'][$i]);
            }
        });

    }

    public function generateStatement($database, $customerId, $debug = false)
    {
        if ($database == 'mssql-toronto') {
            $location = 'Toronto';
        } else {
            $location = 'Mississauga';
        }

        $customer = $this->getCustomer($database, $customerId);

        $startDate = date('M j Y', strtotime("first day of last month"));
        $endDate = date('M j Y', strtotime("tomorrow"));

        $startDateString = "$startDate 12:00:00:000AM";
        $endDateString = "$endDate 12:00:00:000AM";

        $receivableHistory = $this->getAccountReceivableHistoryForCustomer($database, $customerId, $startDateString, $endDateString);
        // $alternateReceivableHistory = $this->getAlternateAccountReceivableHistoryForCustomer($database, $customerId, $startDateString, $endDateString);
        $paymentHistory = $this->getPaymentHistoryForCustomer($database, $customerId, $startDateString, $endDateString);

        $latestTransaction = $this->getAccountReceivableLatestTransactionAmountForCustomer($database, $customerId, $startDateString, $endDateString);

        $data['transactions'] = [];
        $data['payments'] = [];
        $transactionNumbers = [];

        $balance = 0;
        $payments = 0;

        foreach ($receivableHistory as $invoice) {
            $accountReceivableID = $invoice->AccountReceivableID;
            $transactionNumber = $invoice->TransactionNumber;

            $accountReceivableHistoryEntry = $this->getAccountReceivableEntry($database, $accountReceivableID);
            $lineItemsForTransaction = $this->getTransactionLineItems($database, $transactionNumber);
            $orderEntryDescriptions = $this->getOrderEntryDescriptions($database, $transactionNumber);

            $data['transactions'][$transactionNumber]['entry'] = $accountReceivableHistoryEntry;
            $data['transactions'][$transactionNumber]['items'] = $lineItemsForTransaction;
            $data['transactions'][$transactionNumber]['orderentry'] = $orderEntryDescriptions;
            $balance = $balance + $accountReceivableHistoryEntry->Amount;
            $data['transactions'][$transactionNumber]['newBalance'] = $balance;

            $this->generateReceipt($database, $customerId, $transactionNumber);

            $transactionNumbers[] = $transactionNumber;
        }

        foreach ($paymentHistory as $payment) {
            $payments = $payments + $payment->Amount;
            $data['payments'][$payment->FormattedDate]['amount'] = number_format($payment->Amount, 2, '.', '');
        }

        if ($payments < 0) {
            $payments = 0;
        }

        $data['newCharges'] = number_format($latestTransaction, 2, '.', '');
        $data['newPayments'] = number_format($payments, 2, '.', '');

        // package the data for the template
        $data['account'] = $customer;
        $data['receivableHistory'] = $receivableHistory;
        $data['location'] = $location;
        $data['totalCharges'] = $balance;

        // $storagePath = storage_path().'/';
        // $fileName = $customerId.'-statement.pdf';
        // $fullInvoicePath = $storagePath.$fileName;

        // PDF::loadView('emails.statement.accountstatement-pdf', $data)->save($fullInvoicePath);

        $view = View::make('emails.statement.accountstatement', $data);
        $contents = (string) $view;

        $pdfview = View::make('emails.statement.accountstatement-pdf', $data);
        $pdfcontents = (string) $pdfview;

        return array($contents, $customer->AccountBalance, $pdfcontents, $transactionNumbers);
    }

    // make these two a bit cleaner later
    public function overdue()
    {
        $toronto = $this->getOverdueCreditCustomers('mssql-toronto');
        $mississauga = $this->getOverdueCreditCustomers('mssql-squareone');

        $combined = array();

        $cutoff = date('M j Y', strtotime("first day of this month"));
        $cutoffString = "$cutoff 12:00:00:000AM";

        foreach ($mississauga as $customer) {
            $realBalance = $customer->AccountBalance - $this->getAccountReceivableSumForCustomer('mssql-squareone', $customer->ID, $cutoffString);
            $combined[$customer->EmailAddress]['ID'] = $customer->ID;
            $combined[$customer->EmailAddress]['AccountNumber'] = $customer->AccountNumber;
            $combined[$customer->EmailAddress]['balance'] = $realBalance;
        }

        foreach ($toronto as $customer) {
            $realBalance = $customer->AccountBalance - $this->getAccountReceivableSumForCustomer('mssql-toronto', $customer->ID, $cutoffString);

            $combined[$customer->EmailAddress]['ID'] = $customer->ID;
            $combined[$customer->EmailAddress]['AccountNumber'] = $customer->AccountNumber;

            if (isset($combined[$customer->EmailAddress]['balance'])) {
                $combined[$customer->EmailAddress]['balance'] = $combined[$customer->EmailAddress]['balance'] + $realBalance;
            } else {
                $combined[$customer->EmailAddress]['balance'] = $realBalance;
            }
        }

        foreach ($combined as $email => $customer) {
            if ($customer['balance'] > 0.01) {
                $this->sendOverdueNotification($email, $customer['AccountNumber'], $customer['balance']);
                // var_dump($customer);
            }
        }

        return 'ok';
    }

    public function overdueAdmin()
    {
        $toronto = $this->getOverdueCreditCustomers('mssql-toronto');
        $mississauga = $this->getOverdueCreditCustomers('mssql-squareone');

        $combined = array();

        $cutoff = date('M j Y', strtotime("first day of this month"));
        $cutoffString = "$cutoff 12:00:00:000AM";

        foreach ($mississauga as $customer) {
            $realBalance = $customer->AccountBalance - $this->getAccountReceivableSumForCustomer('mssql-squareone', $customer->ID, $cutoffString);
            $combined[$customer->EmailAddress]['ID'] = $customer->ID;
            $combined[$customer->EmailAddress]['AccountNumber'] = $customer->AccountNumber;
            $combined[$customer->EmailAddress]['Company'] = $customer->Company;
            if(!$combined[$customer->EmailAddress]['Company']) {
                $combined[$customer->EmailAddress]['Company'] = $customer->AccountNumber;
            }

            $combined[$customer->EmailAddress]['balance'] = $realBalance;
        }

        foreach ($toronto as $customer) {
            $realBalance = $customer->AccountBalance - $this->getAccountReceivableSumForCustomer('mssql-toronto', $customer->ID, $cutoffString);

            $combined[$customer->EmailAddress]['ID'] = $customer->ID;
            $combined[$customer->EmailAddress]['AccountNumber'] = $customer->AccountNumber;
            $combined[$customer->EmailAddress]['Company'] = $customer->Company;
            if(!$combined[$customer->EmailAddress]['Company']) {
                $combined[$customer->EmailAddress]['Company'] = $customer->AccountNumber;
            }

            if (isset($combined[$customer->EmailAddress]['balance'])) {
                $combined[$customer->EmailAddress]['balance'] = $combined[$customer->EmailAddress]['balance'] + $realBalance;
            } else {
                $combined[$customer->EmailAddress]['balance'] = $realBalance;
            }
        }

        foreach ($combined as $email => $customer) {
            if ($customer['balance'] > 0.01) {
                $this->sendOverdueAdminNotification($email, $customer['Company'], $customer['balance']);
                // var_dump($customer);
            }
        }

        return 'ok';
    }


    public function sendOverdueNotification($email, $company, $accountBalance)
    {
        $data['company'] = $company;
        $data['accountBalance'] = $accountBalance;

        $mailto = array(
            'email' => $email,
            'company' => $company
        );

        Mail::send('emails.statement.overdue', $data, function ($message) use ($mailto) {
            $message->from('service@mg.techknowspace.com', 'Accounts Receivable - The TechKnow Space');
            $message->to($mailto['email'])->subject('Outstanding Balance - '.$mailto['company']);
            $message->cc('b2badmin@techknowspace.com');
        });

    }

    public function sendOverdueAdminNotification($email, $company, $accountBalance)
    {
        $data['company'] = $company;
        $data['accountBalance'] = $accountBalance;

        $mailto = array(
            'email' => $email,
            'company' => $company
        );

        Mail::send('emails.statement.overdueadmin', $data, function ($message) use ($mailto) {
            $message->from('service@mg.techknowspace.com', 'Accounts Receivable - The TechKnow Space');
            $message->to('b2badmin@techknowspace.com')->subject('OVERDUE Balance - '.$mailto['company']);
        });

    }


    public function getCustomer($database, $customerId)
    {
        $query = "SELECT * FROM Customer WHERE ID = $customerId";
        $customer = DB::connection($database)->select($query)[0];

        return $customer;
    }

    public function getPaymentHistoryForCustomer($database, $customerId, $startDate, $endDate)
    {
        $query = "SELECT *,
                  REPLACE(CONVERT(NVARCHAR, [Time], 103), ' ', '/') AS FormattedDate
                  FROM Payment
                  WHERE CustomerID = $customerId
                  AND (Time >= '$startDate')
                  AND (Time < '$endDate')";

        $entries = DB::connection($database)->select($query);

        return $entries;
    }

    public function getAccountReceivableSumForCustomer($database, $customerId, $cutoffDate)
    {
        $query = "SELECT COALESCE( (
                    SELECT SUM(AccountReceivableHistory.Amount)
                    FROM AccountReceivableHistory
                    INNER JOIN AccountReceivable
                      ON AccountReceivable.ID = AccountReceivableHistory.AccountReceivableID
                    WHERE AccountReceivableHistory.Date > '$cutoffDate'
                    AND AccountReceivableHistory.Amount > 0
                    AND AccountReceivable.CustomerID = $customerId
                    AND AccountReceivable.TransactionNumber != 0
                    HAVING (SUM(AccountReceivableHistory.Amount) <> 0)
                    OR (MAX(AccountReceivableHistory.Date) > '$cutoffDate')
                  ), 0) AS Balance";

        $amount = DB::connection($database)->select($query)[0]->Balance;

        return $amount;
    }


    public function getAccountReceivableHistoryForCustomer($database, $customerId, $startDate, $endDate)
    {
        $query = "SELECT
                    AccountReceivableHistory.AccountReceivableID,
                    SUM(AccountReceivableHistory.Amount) AS Balance,
                    AccountReceivableHistory.AccountReceivableID,
                    MAX(AccountReceivable.Type) AS AccountReceivableType,
                    MAX(AccountReceivable.TransactionNumber) AS TransactionNumber,
                    MAX(AccountReceivable.Date) AS [Date],
                    MAX(AccountReceivable.DueDate) AS DueDate,
                    MAX(AccountReceivable.OriginalAmount) AS OriginalAmount
                  FROM AccountReceivableHistory
                  INNER JOIN AccountReceivable
                    ON AccountReceivable.ID = AccountReceivableHistory.AccountReceivableID
                  WHERE AccountReceivableHistory.Date < '$endDate'
                  AND AccountReceivable.Date > '$startDate'
                  AND AccountReceivable.CustomerID = $customerId
                  AND AccountReceivable.TransactionNumber != 0
                  GROUP BY AccountReceivableHistory.AccountReceivableID
                  HAVING (SUM(AccountReceivableHistory.Amount) <> 0)
                  OR (MAX(AccountReceivableHistory.Date) >= '$startDate')
                  ORDER BY [Date]";

        $entries = DB::connection($database)->select($query);

        return $entries;
    }

    public function getAlternateAccountReceivableHistoryForCustomer($database, $customerId, $startDate, $endDate)
    {
        $query = "SELECT
                    AccountReceivableHistory.*,
                    AccountReceivable.Type AS AccountReceivableType,
                    AccountReceivable.TransactionNumber
                  FROM AccountReceivableHistory
                  INNER JOIN AccountReceivable
                    ON AccountReceivable.ID = AccountReceivableHistory.AccountReceivableID
                  WHERE (CustomerID = $customerId)
                  AND (AccountReceivableHistory.Date >= '$startDate')
                  AND (AccountReceivableHistory.Date < '$endDate')
                  AND (AccountReceivable.TransactionNumber != 0)
                  ORDER BY AccountReceivableHistory.Date";

        $entries = DB::connection($database)->select($query);

        return $entries;
    }

    public function getAccountReceivableLatestTransactionForCustomer($database, $customerId, $startDate, $endDate)
    {
        $query = "SELECT COALESCE( (
                    SELECT TOP 1
                    AccountReceivableHistory.*,
                    AccountReceivable.Type AS AccountReceivableType,
                    AccountReceivable.TransactionNumber
                  FROM AccountReceivableHistory
                  INNER JOIN AccountReceivable
                    ON AccountReceivable.ID = AccountReceivableHistory.AccountReceivableID
                  WHERE (CustomerID = $customerId)
                  AND (AccountReceivableHistory.Date >= '$startDate')
                  AND (AccountReceivableHistory.Date < '$endDate')
                  AND (AccountReceivable.TransactionNumber != 0)
                  ORDER BY AccountReceivableHistory.Date DESC), 0) AS Amount";

        $entry = DB::connection($database)->select($query)[0]->Amount;

        return $entry;
    }

    public function getAccountReceivableLatestTransactionAmountForCustomer($database, $customerId, $startDate, $endDate)
    {
        $query = "SELECT COALESCE( (
                    SELECT SUM(AccountReceivableHistory.Amount)
                  FROM AccountReceivableHistory
                  INNER JOIN AccountReceivable
                    ON AccountReceivable.ID = AccountReceivableHistory.AccountReceivableID
                  WHERE (CustomerID = $customerId)
                  AND (AccountReceivableHistory.Amount > 0)
                  AND (AccountReceivableHistory.Date >= '$startDate')
                  AND (AccountReceivableHistory.Date < '$endDate')
                  AND (AccountReceivable.TransactionNumber != 0)
                  ), 0) AS Amount";

        $entry = DB::connection($database)->select($query)[0]->Amount;

        return $entry;
    }

    public function getAccountReceivableEntry($database, $accountReceivableID)
    {
        $query = "SELECT
                    AccountReceivableHistory.ID,
                    StoreID,
                    AccountReceivableID,
                    BatchNumber,
                    Date,
                    REPLACE(CONVERT(NVARCHAR, [Date], 103), ' ', '/') AS FormattedDate,
                    HistoryType,
                    Comment,
                    Amount,
                    CashierID,
                    PaymentID,
                    TransferArID,
                    RemoteStoreID,
                    RemotePaymentID
                  FROM AccountReceivableHistory
                  WHERE AccountReceivableID = $accountReceivableID
                  ORDER BY Date, ID";

        $entry = DB::connection($database)->select($query)[0];

        return $entry;
    }

    public function getTransactionLineItems($database, $transactionNumber)
    {
        $query = "SELECT
                    ISNULL(Item.ItemLookupCode, '') AS ItemLookupCode,
                    ISNULL(Item.Description, '') AS Description,
                    TransactionEntry.Comment,
                    TransactionEntry.Price
                  FROM TransactionEntry
                  LEFT OUTER JOIN Item
                    ON TransactionEntry.ItemID = Item.ID
                  WHERE (TransactionEntry.TransactionNumber = $transactionNumber)
                  AND ItemID NOT IN (
                    SELECT OrderEntry.ItemID FROM OrderEntry, TransactionEntry, OrderHistory
                    WHERE TransactionEntry.TransactionNumber = $transactionNumber
                    AND OrderHistory.TransactionNumber = $transactionNumber
                    AND OrderHistory.OrderID = OrderEntry.OrderID
                  )
                  ORDER BY TransactionEntry.ID";

        $items = DB::connection($database)->select($query);

        return $items;
    }

    public function getOrder($database, $orderId)
    {
        $query = "SELECT * FROM [Order] WHERE ID = $orderId";

        $result = DB::connection($database)->select($query);

        if(isset($result[0])) {
            return $result[0];
        } else {
            return [];
        }
    }

    public function getOrderEntryDescriptions($database, $transactionNumber)
    {
        $query = "SELECT OrderEntry.Description, REPLACE(OrderEntry.Comment, 'CREP', '') AS Comment, OrderEntry.OrderID, OrderEntry.Price FROM OrderHistory, OrderEntry
                  WHERE OrderHistory.TransactionNumber = $transactionNumber
                  AND OrderEntry.OrderID = OrderHistory.OrderID
                  ORDER BY OrderEntry.ID ASC";

        $descriptions = DB::connection($database)->select($query);

        return $descriptions;
    }

    public function getTransaction($database, $transactionNumber)
    {
        $query = "SELECT * FROM [Transaction]
                  WHERE TransactionNumber = $transactionNumber";

        $transaction = DB::connection($database)->select($query)[0];

        return $transaction;
    }

    public function getTransactionDate($database, $transactionNumber)
    {
        $query = "SELECT convert(varchar, Time, 107) AS Time FROM [Transaction]
                  WHERE TransactionNumber = $transactionNumber";

        $transaction = DB::connection($database)->select($query)[0];

        return $transaction;
    }

    public function getTender($database, $transactionNumber)
    {
        $query = "SELECT * FROM [TenderEntry]
                  WHERE TransactionNumber = $transactionNumber";

        $tender = DB::connection($database)->select($query);

        return $tender;
    }

    public function getTenderSum($database, $transactionNumber)
    {
        $query = "SELECT SUM() FROM [TenderEntry]
                  WHERE TransactionNumber = $transactionNumber";

        $tender = DB::connection($database)->select($query)[0];

        return $tender;
    }

    public function getOverdueCreditCustomers($database)
    {
        $query = "SELECT ID, Company, AccountNumber, FirstName, LastName, PhoneNumber, AccountBalance, CreditLimit, LastVisit, EmailAddress, CustomText1, CustomText2, CustomText3, Email2
                  FROM Customer
                  WHERE CreditLimit > 0
                  AND AccountBalance > 0
                  ORDER BY AccountBalance DESC";

        return DB::connection($database)->select($query);
    }

    public function getCreditCustomers($database)
    {
        $query = "SELECT ID, AccountNumber, Company, FirstName, LastName, PhoneNumber, AccountBalance, CreditLimit, LastVisit, EmailAddress, CustomText1, CustomText2, CustomText3, Email2, TotalSales
                  FROM Customer
                  WHERE CreditLimit > 0
                  ORDER BY AccountBalance DESC";

        return DB::connection($database)->select($query);
    }
}
