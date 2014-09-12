<?php

class RecordsController extends BaseController {

    public function index()
    {
        return $queryFirstDay.' - '.$queryLastDay;
    }

    /*
        PAGE FUNCTIONS
    */
    public function showWeeklyRepeat()
    {
        $start = '20140501';
        $end = '20140507';
        // $start = '20140801';
        // $end = '20140807';

        $queryResults = $this->totalCustomerQueryRange($start, $end);

        $total['m'] = $queryResults[0];
        $total['t'] = $queryResults[1];

        $queryResults = $this->repeatCustomerQueryRange($start, $end);

        $repeats['m'] = $queryResults[0];
        $repeats['t'] = $queryResults[1];

        $torontoTotalCount = count($total['t']);
        $mississaugaTotalCount = count($total['m']);

        $torontoRepeatCount = count($repeats['t']);
        $mississaugaRepeatCount = count($repeats['m']);

        $torontoPercentage = $this->percentage($torontoRepeatCount, $torontoTotalCount, 2);
        $mississaugaPercentage = $this->percentage($mississaugaRepeatCount, $mississaugaTotalCount, 2);


        $outputString = '<pre>';
        $outputString .= "SquareOne total customers - $mississaugaTotalCount <br>";
        $outputString .= "SquareOne repeat customers - $mississaugaRepeatCount - $mississaugaPercentage% repeats<br><br>";
        $outputString .= "Toronto total customers - $torontoTotalCount <br>";
        $outputString .= "Toronto repeat customers - $torontoRepeatCount - $torontoPercentage% repeats <br>";
        $outputString .= '</pre>';

        $data['daterange'] = "$start - $end";
        $data['result'] = $outputString;
        $data['graphdata'] = '';

        return View::make('reports.repeatcustomers', $data);
    }

    /* Display the repeat customers for a given month */
    public function showMonthlyRepeat()
    {
        $m = Input::get('m');
        $y = Input::get('y');

        $month = $m ? $m : date('F');
        $year = $y ? $y : date('Y');

        $total = $this->getTotalCustomersForMonth($month, $year);
        $repeats = $this->getRepeatCustomersForMonth($month, $year);
        
        $torontoTotalCount = count($total['t']);
        $mississaugaTotalCount = count($total['m']);

        $torontoRepeatCount = count($repeats['t']);
        $mississaugaRepeatCount = count($repeats['m']);

        $torontoPercentage = $this->percentage($torontoRepeatCount, $torontoTotalCount, 2);
        $mississaugaPercentage = $this->percentage($mississaugaRepeatCount, $mississaugaTotalCount, 2);

        $outputString = '<pre>';
        $outputString .= "SquareOne total customers - $mississaugaTotalCount <br>";
        $outputString .= "SquareOne repeat customers - $mississaugaRepeatCount - $mississaugaPercentage% repeats<br><br>";
        $outputString .= "Toronto total customers - $torontoTotalCount <br>";
        $outputString .= "Toronto repeat customers - $torontoRepeatCount - $torontoPercentage% repeats <br>";
        $outputString .= '</pre>';

        $data['showpicker'] = 1;
        $data['daterange'] = '';
        $data['result'] = $outputString;
                $data['graphdata'] = '';


        return View::make('reports.repeatcustomers', $data);
    }    

    /*
        GRUNTS
    */

    /* Returns a full month of customer counts */
    protected function getTotalCustomersForMonth($month, $year)
    {
        $queryFirstDay = date('Ymd', strtotime("first day of $month $year"));
        $queryLastDay = date('Ymd', strtotime("last day of $month $year"));

        $queryResults = $this->totalCustomerQueryRange($queryFirstDay, $queryLastDay);

        $return['m'] = $queryResults[0];
        $return['t'] = $queryResults[1];

        return $return;
    }

    /* Returns a full month of customer repeats */
    protected function getRepeatCustomersForMonth($month, $year)
    {
        $queryFirstDay = date('Ymd', strtotime("first day of $month $year"));
        $queryLastDay = date('Ymd', strtotime("last day of $month $year"));

        $queryResults = $this->repeatCustomerQueryRange($queryFirstDay, $queryLastDay);

        $return['m'] = $queryResults[0];
        $return['t'] = $queryResults[1];

        return $return;
    }

    /* Detaching query logic so these can be easily invoked for specific dates and ranges */
    protected function totalCustomerQueryRange($startDate, $endDate)
    {
        $query = "SELECT DISTINCT CustomerID FROM [Order] 
                WHERE CustomerID IN 
                (
                    SELECT CustomerID FROM [Order]
                    WHERE Time >= '$startDate'
                    AND Time <= '$endDate'
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
                AND Time <= '$endDate'

                ORDER BY CustomerID";

        $results[0] = DB::connection('mssql-squareone')->select($query);
        $results[1] = DB::connection('mssql-toronto')->select($query);

        $results = $this->addLocation($results);

        return $results;
    }

    protected function repeatCustomerQueryRange($startDate, $endDate)
    {
        $query = "SELECT DISTINCT CustomerID FROM [Order] 
                WHERE CustomerID IN 
                (
                    SELECT CustomerID FROM [Order]
                    WHERE CustomerID IN 
                    (
                        SELECT CustomerID FROM [Order]
                        WHERE Time >= '$startDate'
                        AND Time <= '$endDate'
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
                AND Time <= DATEADD(DAY, DATEDIFF(DAY, 0, '$endDate'), -1)
                ORDER BY CustomerID";

        $results[0] = DB::connection('mssql-squareone')->select($query);
        $results[1] = DB::connection('mssql-toronto')->select($query);

        $results = $this->addLocation($results);

        return $results;
    }

    /* Add location tag to supplied array of results */
    public function addLocation($results)
    {
        foreach ($results[0] as $result) {
            $result->Location = 'm';
        }

        foreach ($results[1] as $result) {
            $result->Location = 't';
        }

        return $results;
    }

    /* Calculate percentage of values, with decimal precision */
    public function percentage($val1, $val2, $precision) 
    {
        $res = round( ($val1 / $val2) * 100, $precision );
        return $res;
    }
}