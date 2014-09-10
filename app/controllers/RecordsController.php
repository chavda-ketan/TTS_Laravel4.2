<?php

class RecordsController extends BaseController {

    public function index()
    {
        $m = Input::get('m');
        $y = Input::get('y');

        $month = $m ? $m : date('F');
        $year = $y ? $y : date('Y');

        $queryFirstDay = date('Ymd', strtotime("first day of $month $year"));
        $queryLastDay = date('Ymd', strtotime("last day of $month $year"));

        return $queryFirstDay.' - '.$queryLastDay;
    }

    /* Display the repeat customers for a given month */
    public function showRepeat()
    {
        $m = Input::get('m');
        $y = Input::get('y');

        $month = $m ? $m : date('m');
        $year = $y ? $y : date('Y');

        $total = $this->getTotalCustomersForMonth($month, $year);
        $repeats = $this->getRepeatCustomersForMonth($month, $year);
        
        $torontoTotalCount = count($total['t']);
        $mississaugaTotalCount = count($total['m']);

        $torontoRepeatCount = count($repeats['t']);
        $mississaugaRepeatCount = count($repeats['m']);

        $torontoPercentage = ($torontoRepeatCount / $torontoTotalCount) * 100;
        $mississaugaPercentage = ($mississaugaRepeatCount / $mississaugaTotalCount) * 100;

        $outputString = '<pre>';
        $outputString .= "SquareOne total customers - $mississaugaTotalCount <br>";
        $outputString .= "SquareOne repeat customers - $mississaugaRepeatCount - %$mississaugaPercentage repeats<br><br>";
        $outputString .= "Toronto total customers - $torontoTotalCount <br>";
        $outputString .= "Toronto repeat customers - $torontoRepeatCount - %$torontoPercentage repeats <br>";
        $outputString .= '</pre>';

        return $outputString;
    }

    protected function getTotalCustomersForMonth($month, $year)
    {
        $queryFirstDay = date('Ymd', strtotime("first day of $month $year"));
        $queryLastDay = date('Ymd', strtotime("last day of $month $year"));

        $query = "SELECT DISTINCT CustomerID FROM [Order] 
                WHERE CustomerID IN 
                (
                    SELECT CustomerID FROM [Order]
                    WHERE Time >= '20140901'
                    AND Time < $queryLastDay
                )
                AND Closed = 1
                AND ID IN
                (
                    SELECT OrderHistory.OrderID FROM OrderHistory
                )
                AND ID NOT IN
                (
                    SELECT OrderEntryID FROM OrderRework
                )
                ORDER BY CustomerID";


        $mississaugaResults = DB::connection('mssql-squareone')->select($query);
        $torontoResults = DB::connection('mssql-toronto')->select($query);

        foreach ($mississaugaResults as $result) {
            $result->Location = 'm';
        }

        foreach ($torontoResults as $result) {
            $result->Location = 't';
        }

        $return['m'] = $mississaugaResults;
        $return['t'] = $torontoResults;

        return $return;
    }

    protected function getRepeatCustomersForMonth($month, $year)
    {
        // $query = "SELECT CustomerID, Time, (SELECT COUNT(DISTINCT CustomerID)) FROM [Order] 
        //         WHERE CustomerID IN
        //         (
        //             SELECT CustomerID FROM [Order]
        //             WHERE CustomerID IN 
        //             (
        //                 SELECT CustomerID FROM [Order]
        //                 WHERE Time >= '20140801'
        //                 AND Time < GETDATE()
        //             )
        //             GROUP BY CustomerID
        //             HAVING ( COUNT(CustomerID) > 1 )
        //         )
        //         AND Closed = 1
        //         ORDER BY CustomerID, ID";

        $query = "SELECT DISTINCT CustomerID FROM [Order] 
                WHERE CustomerID IN 
                (
                    SELECT CustomerID FROM [Order]
                    WHERE CustomerID IN 
                    (
                        SELECT CustomerID FROM [Order]
                        WHERE Time >= '20140901'
                        AND Time < GETDATE()
                    )
                    GROUP BY CustomerID
                    HAVING ( COUNT(CustomerID) > 1 )
                )
                AND Closed = 1
                AND ID IN
                (
                    SELECT OrderHistory.OrderID FROM OrderHistory
                )
                AND ID NOT IN
                (
                    SELECT OrderEntryID FROM OrderRework
                )
                ORDER BY CustomerID";


        $mississaugaResults = DB::connection('mssql-squareone')->select($query);
        $torontoResults = DB::connection('mssql-toronto')->select($query);

        foreach ($mississaugaResults as $result) {
            $result->Location = 'm';
        }

        foreach ($torontoResults as $result) {
            $result->Location = 't';
        }

        $return['m'] = $mississaugaResults;
        $return['t'] = $torontoResults;

        return $return;
    }
}