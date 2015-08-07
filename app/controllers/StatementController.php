<?php

class StatementController extends BaseController {
    public function bulkReceiptCron()
    {

    }

    // public function accountStatementCronTest()
    // {
    //     $customersS1 = DB::connection('mssql-squareone')->select("SELECT * FROM Customer WHERE AccountNumber = '905 897 9860'");
    //     $customersTO = DB::connection('mssql-toronto')->select("SELECT * FROM Customer WHERE AccountNumber = '905 897 9860'");

    //     foreach ($customersS1 as $customer) {
    //         $this->generateStatement('mssql-squareone', $customer->ID);
    //     }

    //     foreach ($customersTO as $customer) {
    //         $this->generateStatement('mssql-toronto', $customer->ID);
    //     }
    // }

    public function accountStatementCron()
    {
        // $customersS1 = DB::connection('mssql-squareone')->select("SELECT * FROM Customer WHERE Balance > 0");
        // $customersTO = DB::connection('mssql-toronto')->select("SELECT * FROM Customer WHERE Balance > 0");
        $customersS1 = DB::connection('mssql-squareone')->select("SELECT * FROM Customer WHERE AccountNumber = '905 897 9860'");
        $customersTO = DB::connection('mssql-toronto')->select("SELECT * FROM Customer WHERE AccountNumber = '905 897 9860'");

        $statements = [];

        foreach ($customersS1 as $customer) {
            $rendered = $this->generateStatement('mssql-squareone', $customer->ID);
            $statements[$customer->EmailAddress]['statement'][] = $rendered[0];
            $statements[$customer->EmailAddress]['attachment'][] = $rendered[2];
            $statements[$customer->EmailAddress]['pdf'][] = $rendered[3];
            $statements[$customer->EmailAddress]['balance'] = $rendered[1];
        }

        foreach ($customersTO as $customer) {
            $rendered = $this->generateStatement('mssql-toronto', $customer->ID);
            $statements[$customer->EmailAddress]['statement'][] = $rendered[0];
            $statements[$customer->EmailAddress]['attachment'][] = $rendered[2];
            $statements[$customer->EmailAddress]['pdf'][] = $rendered[3];

            if (isset($statements[$customer->EmailAddress]['balance'])) {
                $statements[$customer->EmailAddress]['balance'] = $statements[$customer->EmailAddress]['balance'] + $rendered[1];
            } else {
                $statements[$customer->EmailAddress]['balance'] = $rendered[1];
            }
        }

        foreach ($statements as $email => $statement) {
            return $this->sendConsolidatedStatements($email, $statement);
            // print_r($statement);
        }

    }

    public function accountReceiptCron()
    {
        $customersS1 = DB::connection('mssql-squareone')->select("SELECT * FROM Customer WHERE Balance > 0");
        $customersTO = DB::connection('mssql-toronto')->select("SELECT * FROM Customer WHERE Balance > 0");

        foreach ($customersS1 as $customer) {
            $this->generateStatement('mssql-squareone', $customer->ID);
        }

        foreach ($customersTO as $customer) {
            $this->generateStatement('mssql-toronto', $customer->ID);
        }
    }

    public function sendConsolidatedStatements($email, $statements)
    {
        $paths = $statements['attachment'];
        $mailto = array(
            'email' => $email,
            'path' => $paths
        );

        $data['statements'] = $statements['statement'];
        $data['balance'] = $statements['balance'];


        Mail::send('emails.statement.combinedstatement', $data, function ($message) use ($mailto) {
            $message->from('service@techknowspace.com', 'TechKnow Space');
            $message->to($mailto['email'])->subject('Account Statement');

            $size = sizeOf($mailto['path']);

            for($i=0; $i < $size; $i++){
                $message->attach($mailto['path'][$i]);
            }
        });

        return View::make('emails.statement.combinedstatement', $data);
    }

    public function generateReceipt($customerId, $transactionNumber)
    {
        $database = 'mssql-squareone';

        $customer = $this->getCustomer($database, $customerId);
        $transaction = $this->getTransaction($database, $transactionNumber);
        $lineItemsForTransaction = $this->getTransactionLineItems($database, $transactionNumber);
        $orderEntryDescriptions = $this->getOrderEntryDescriptions($database, $transactionNumber);
        $order = $this->getOrder($database, $orderEntryDescriptions[0]->OrderID);

        $data['transaction'] = $transaction;
        $data['transactionNumber'] = $transactionNumber;
        $data['customerId'] = $customerId;
        $data['account'] = $customer;
        $data['lineItems'] = $lineItemsForTransaction;
        $data['orderEntry'] = $orderEntryDescriptions;
        $data['order'] = $order;

        return View::make('emails.statement.receipt', $data);
    }

    public function generateStatement($database, $customerId)
    {
        if ($database == 'mssql-toronto') {
            $location = 'Toronto';
        } else {
            $location = 'Mississauga';
        }

        // $customerId = 22261;
        // test customerId
        $customer = $this->getCustomer($database, $customerId);

        $startDate = date('M j Y', strtotime("first day of last month +15 days"));
        $endDate = date('M j Y', strtotime("tomorrow"));

        $startDateString = "$startDate 12:00:00:000AM";
        $endDateString = "$endDate 12:00:00:000AM";

        $receivableHistory = $this->getAccountReceivableHistoryForCustomer($database, $customerId, $startDateString, $endDateString);
        $latestTransaction = $this->getAccountReceivableLatestTransactionForCustomer($database, $customerId, $startDateString, $endDateString);

        $data['transactions'] = [];

        $balance = 0;

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
        }

        if ($latestTransaction->Amount > 0) {
            $data['newCharges'] = number_format($latestTransaction->Amount, 2, '.', '');
            $data['newPayments'] = number_format(0.00, 2, '.', '');
        } else {
            $data['newCharges'] = number_format(0.00, 2, '.', '');
            $data['newPayments'] = number_format($latestTransaction->Amount, 2, '.', '');
        }

        // package the data for the template
        $data['account'] = $customer;
        $data['receivableHistory'] = $receivableHistory;
        $data['location'] = $location;

        $storagePath = storage_path().'/';
        $fileName = $customerId.'-statement.pdf';
        $fullInvoicePath = $storagePath.$fileName;

        PDF::loadView('emails.statement.accountstatement-pdf', $data)->save($fullInvoicePath);

        $view = View::make('emails.statement.accountstatement', $data);
        $contents = (string) $view;

        $pdfview = View::make('emails.statement.accountstatement-pdf', $data);
        $pdfcontents = (string) $pdfview;

        return array($contents, $customer->AccountBalance, $fullInvoicePath, $pdfcontents);
    }


    public function overdueNotification($database, $customerId)
    {
        $data = [];
        return View::make('emails.statement.overdue', $data);
    }


    public function getCustomer($database, $customerId)
    {
        $query = "SELECT * FROM Customer WHERE ID = $customerId";
        $customer = DB::connection($database)->select($query)[0];

        return $customer;
    }

    public function blobTest()
    {
        $data = DB::connection('mssql-squareone')->select("SELECT TOP 1 ReceiptCompressed FROM [dbo].[Journal] ORDER BY ID Desc ")[0];
        return $data->ReceiptCompressed;
    }

    public function getAccountReceivableHistoryForCustomer($database, $customerId, $startDate, $endDate)
    {
        $query = "SELECT
                    0 AS StoreID,
                    AccountReceivableHistory.AccountReceivableID,
                    SUM(AccountReceivableHistory.Amount) AS Balance,
                    AccountReceivableHistory.AccountReceivableID,
                    MAX(AccountReceivable.Type) AS AccountReceivableType,
                    MAX(AccountReceivable.TransactionNumber) AS TransactionNumber,
                    MAX(AccountReceivable.Date) AS [Date],
                    MAX(AccountReceivable.DueDate) AS DueDate,
                    MAX(AccountReceivable.OriginalAmount) AS OriginalAmount,
                    '' AS ReferenceNumber,
                    '' AS StoreName,
                    '' AS StoreCode
                  FROM AccountReceivableHistory
                  INNER JOIN AccountReceivable
                    ON AccountReceivable.ID = AccountReceivableHistory.AccountReceivableID
                  WHERE AccountReceivableHistory.Date < '$endDate'
                  AND AccountReceivable.CustomerID = $customerId
                  AND AccountReceivable.TransactionNumber != 0
                  GROUP BY AccountReceivableHistory.AccountReceivableID
                  HAVING (SUM(AccountReceivableHistory.Amount) <> 0)
                  OR (MAX(AccountReceivableHistory.Date) >= '$startDate')
                  ORDER BY [Date]";

        $entries = DB::connection($database)->select($query);

        return $entries;
    }

    public function getAccountReceivableLatestTransactionForCustomer($database, $customerId, $startDate, $endDate)
    {
        $query = "SELECT TOP 1
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

        $entry = DB::connection($database)->select($query)[0];

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
                    TransactionEntry.Comment
                  FROM TransactionEntry
                  LEFT OUTER JOIN Item
                    ON TransactionEntry.ItemID = Item.ID
                  WHERE (TransactionEntry.TransactionNumber = $transactionNumber)
                  ORDER BY TransactionEntry.ID";

        $items = DB::connection($database)->select($query);

        return $items;
    }

    public function getOrder($database, $orderId)
    {
        $query = "SELECT * FROM [Order] WHERE ID = $orderId";

        $order = DB::connection($database)->select($query)[0];

        return $order;
    }

    public function getOrderEntryDescriptions($database, $transactionNumber)
    {
        $query = "SELECT * FROM OrderHistory, OrderEntry
                  WHERE OrderHistory.TransactionNumber = $transactionNumber
                  AND OrderEntry.OrderID = OrderHistory.OrderID";

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

}
