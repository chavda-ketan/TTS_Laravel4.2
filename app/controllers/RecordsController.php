<?php

class RecordsController extends BaseController
{

    public function index()
    {
        return $this->showRepeatCustomerReport();
    }

/**
PAGE FUNCTIONS
 */

    public function showRepeatCustomerReport()
    {
        $start = Input::get('start');
        $end = Input::get('end');

        $data['start'] = $start ? $start : date('Y-m-d', strtotime("30 days ago"));
        $data['end'] = $end ? $end : date('Y-m-d');

        $end = new DateTime($data['end']);
        $today = new DateTime();

        if ($end > $today) {
            $data['end'] = date('Y-m-d');
        }

        $dailyTotals = $this->iterateOverDateRange($data);

        /* Instantiate all the things */
        $data['trends'] = array();

        $data['dates'] = $data['total'] =  $data['repeat'] = $data['referral'] =
        $data['revenue'] = $data['spend'] = $data['chartdata'] =
        $data['trends']['search'] = $data['trends']['repeat'] = $data['trends']['referral'] = '';

        $days = $absoluteSearchTotal = $absoluteRepeatTotal = $absoluteReferralTotal = 0;

        foreach ($dailyTotals as $day => $metrics) {
            $days++;
            $total = count($metrics['total']['t']) + count($metrics['total']['m']);
            $repeat = count($metrics['repeat']['t']) + count($metrics['repeat']['m']);
            $referral = count($metrics['referral']['t']) + count($metrics['referral']['m']);

            /* Work order revenue */
            $revenue = $this->getRevenueForDate($day);
            $combinedRevenue = $revenue['t']->Revenue + $revenue['m']->Revenue;
            $dividedRevenue = round($combinedRevenue / $total, 2);

            $search = $total - $repeat - $referral;

            /* Absolute totals */
            $absoluteSearchTotal = $search + $absoluteSearchTotal;
            $absoluteRepeatTotal = $repeat + $absoluteRepeatTotal;
            $absoluteReferralTotal = $referral + $absoluteReferralTotal;

            /* Adwords spend */
            $adwordsPerCustomer = 0;
            $adwordsCost = $this->getAdwordsCostForDate($day);

            if ($adwordsCost) {
                $adwordsPerCustomer = $adwordsCost / $search;
                $adwordsPerCustomer = round($adwordsPerCustomer, 2);
            }

            /* Trending averages */
            $trendSearch = round($absoluteSearchTotal / $days, 1);
            $trendRepeat = round($absoluteRepeatTotal / $days, 1);
            $trendReferral = round($absoluteReferralTotal / $days, 1);

            $data['dates'] .= "'$day', ";
            $data['total'] .= "$search, ";
            $data['repeat'] .= "$repeat, ";
            $data['referral'] .= "$referral, ";
            $data['revenue'] .= "$dividedRevenue, ";
            $data['spend'] .= "$adwordsPerCustomer, ";

            $data['trends']['search'] .= "$trendSearch,";
            $data['trends']['repeat'] .= "$trendRepeat,";
            $data['trends']['referral'] .= "$trendReferral,";
        }

        $data['avg'] = $this->getAverages($absoluteSearchTotal, $absoluteRepeatTotal,
                                          $absoluteReferralTotal, $days,
                                          $data['start'], $data['end']);

        $data['showpicker'] = 1;

        return View::make('reports.repeatcustomers', $data);
    }

/**
MATH FUNCTIONS
 */

    protected function getAverages($absoluteSearchTotal, $absoluteRepeatTotal, $absoluteReferralTotal, $days, $start, $end)
    {
        $average = array();
        $rangeSpend = $this->getAdwordsRangeAverage($start, $end);

        $rangeAverageSpend = $rangeSpend / $absoluteSearchTotal;
        $averages['adwords'] = round($rangeAverageSpend, 2);

        $averageSearch = $absoluteSearchTotal / $days;
        $averages['search'] = round($averageSearch, 2);

        $averageRepeat = $absoluteRepeatTotal / $days;
        $averages['repeat'] = round($averageRepeat, 2);

        $averageReferral = $absoluteReferralTotal / $days;
        $averages['referral'] = round($averageReferral, 2);

        return $averages;
    }


    protected function getAveragesTrend($absoluteSearchTotal, $absoluteRepeatTotal, $absoluteReferralTotal, $days, $start, $end)
    {


        return $averagesTrend;
    }

/**
DATE RANGE FUNCTIONS
 */

    protected function iterateOverDateRange($dates)
    {
        $begin = new DateTime($dates['start']);
        $end = new DateTime($dates['end']);

        /* add one day to fix oddity */
        $end->add(new DateInterval('P1D'));

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $rangeOutput = array();

        foreach ($period as $day) {
            $date = $day->format("Y-m-d");

            $rangeOutput[$date]['total'] = $this->getTotalCustomersForDay($day);
            $rangeOutput[$date]['repeat'] = $this->getRepeatCustomersForDay($day);
            $rangeOutput[$date]['referral'] = $this->getReferralCustomersForDay($day);
        }

        return $rangeOutput;
    }

    /* Input a month and year, get an array with the first and last calendar date in SQL Server format */
    protected function monthDateRange($month, $year)
    {
        $firstDay = date('Y-m-d 00:00:00.000', strtotime("first day of $month $year"));
        $lastDay = date('Y-m-d 23:59:59.000', strtotime("last day of $month $year"));

        $return['first'] = $firstDay;
        $return['last'] = $lastDay;

        return $return;
    }

    /* Month */
    protected function getTotalCustomersForMonth($month, $year)
    {
        $dates = $this->monthDateRange($month, $year);
        $queryResults = $this->totalCustomerQueryRange($dates['first'], $dates['last']);
        return $queryResults;
    }

    protected function getRepeatCustomersForMonth($month, $year)
    {
        $dates = $this->monthDateRange($month, $year);
        $queryResults = $this->repeatCustomerQueryRange($dates['first'], $dates['last']);
        return $queryResults;
    }

    protected function getReferralCustomersForMonth($month, $year)
    {
        $dates = $this->monthDateRange($month, $year);
        $queryResults = $this->referralCustomerQueryRange($dates['first'], $dates['last']);
        return $queryResults;
    }

    /* Day */
    protected function getTotalCustomersForDay($day)
    {
        $start = $day->format("Y-m-d 00:00:00.000");
        $end = $day->format("Y-m-d 23:59:59.000");
        $queryResults = $this->totalCustomerQueryRange($start, $end);
        return $queryResults;
    }

    protected function getRepeatCustomersForDay($day)
    {
        $start = $day->format("Y-m-d 00:00:00.000");
        $end = $day->format("Y-m-d 23:59:59.000");
        $queryResults = $this->repeatCustomerQueryRange($start, $end);
        return $queryResults;
    }

    protected function getReferralCustomersForDay($day)
    {
        $start = $day->format("Y-m-d 00:00:00.000");
        $end = $day->format("Y-m-d 23:59:59.000");
        $queryResults = $this->referralCustomerQueryRange($start, $end);
        return $queryResults;
    }

/**
DATABASE QUERIES
 */

    protected function totalCustomerQueryRange($startDate, $endDate)
    {
        $query = "SELECT DISTINCT CustomerID FROM [Order]
                WHERE CustomerID IN
                (
                    SELECT CustomerID FROM [Order]
                    WHERE Time >= '$startDate'
                    AND Time <= '$endDate'
                )
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

        $results['m'] = DB::connection('mssql-squareone')->select($query);
        $results['t'] = DB::connection('mssql-toronto')->select($query);

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
                    AND Time <= '$endDate'
                    GROUP BY CustomerID
                    HAVING ( COUNT(CustomerID) > 1 )
                )
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

        $results['m'] = DB::connection('mssql-squareone')->select($query);
        $results['t'] = DB::connection('mssql-toronto')->select($query);

        $results = $this->addLocation($results);

        return $results;
    }

    protected function referralCustomerQueryRange($startDate, $endDate)
    {
        $query = "SELECT DISTINCT CustomerID FROM [Order]
                WHERE CustomerID IN
                (
                        SELECT CustomerID FROM [Order]
                        WHERE Time >= '$startDate'
                        AND Time <= '$endDate'
                )
                AND ID IN
                (
                    SELECT OrderHistory.OrderID FROM OrderHistory
                )
                AND ID IN
                (
                    SELECT OrderID From OrderEntry WHERE Comment LIKE '%CREF%'
                )
                AND ID NOT IN
                (
                    SELECT OrderEntryID FROM OrderRework
                )
                AND Time <= '$endDate'
                ORDER BY CustomerID";

        $results['m'] = DB::connection('mssql-squareone')->select($query);
        $results['t'] = DB::connection('mssql-toronto')->select($query);

        $results = $this->addLocation($results);

        return $results;
    }

    protected function getRevenueForDate($date)
    {
        $services = '3133, 3134, 3033, 3203, 3197, 3195, 3167, 3168, 3141, 3135, 3173, 3172, 3136, 3201, 3140, 3137, 3200, 7430, 7432, 7752, 7603, 7601, 7599, 1111';
        $query = "SELECT sum(TransactionEntry.Price * Quantity + TransactionEntry.SalesTax) AS Revenue
                  FROM [Transaction], TransactionEntry
                  WHERE TransactionEntry.TransactionNumber = [Transaction].TransactionNumber
                  AND [Transaction].Time >= '$date 00:00:00'
                  AND [Transaction].Time < '$date 23:59:59'
                  AND TransactionEntry.itemid IN ($services)";

        $results['m'] = DB::connection('mssql-squareone')->select($query)[0];
        $results['t'] = DB::connection('mssql-toronto')->select($query)[0];

        return $results;
    }

    protected function getAdwordsCostForDate($date)
    {
        $query = "SELECT sum(cost)/1000000 AS sum FROM adword_performance_hour WHERE DATE='$date'";
        $results = DB::connection('mysql')->select($query)[0]->sum;

        return $results;
    }

    protected function getAdwordsRangeAverage($from, $to)
    {
        $query = "SELECT sum(cost)/1000000 AS sum FROM adword_performance_hour WHERE DATE BETWEEN '$from' AND '$to'";
        $results = DB::connection('mysql')->select($query)[0]->sum;

        return $results;
    }


/**
UTILITY FUNCTIONS
 */

    /* Add location tag to supplied array of results */
    public function addLocation($results)
    {
        foreach ($results['m'] as $result) {
            $result->Location = 'm';
        }

        foreach ($results['t'] as $result) {
            $result->Location = 't';
        }

        return $results;
    }

    /* Calculate percentage of values, with decimal precision */
    public function percentage($val1, $val2, $precision)
    {
        $res = round(($val1 / $val2) * 100, $precision);
        return $res;
    }
}
