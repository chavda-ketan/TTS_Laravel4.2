 <?php

class RecordsController extends BaseController
{

    public function index()
    {
        return $this->showRepeatCustomerReport();
    }

    public function debug()
    {
        $today = new DateTime();

        $count['total'] = $this->getTotalCustomersForDay($today);
        $count['repeat'] = $this->getRepeatCustomersForDay($today);
        $count['referral'] = $this->getReferralCustomersForDay($today);

        return var_dump($count);
    }

/**
PAGE FUNCTIONS
 */

    public function showDeltaReport()
    {
        $data['seo'] = $data['ppc'] = '';

        $dates = $this->getDelta();

        foreach ($dates as $day) {
            $seo = $day->organic;
            $ppc = $day->cpc;

            if ($seo > $ppc) {
                $seo = $seo - $ppc;
                $ppc = 0;
            } elseif ($ppc > $seo) {
                $ppc = $ppc - $seo;
                $seo = 0;
            } elseif ($ppc == $seo) {
                $ppc = $seo = 0;
            }

            $date = $day->date;
            $utcexplode = explode('-', $date);
            $utcexplode[1]--;
            $utc = "Date.UTC($utcexplode[0],$utcexplode[1],$utcexplode[2])";

            $data['seo'] .= "[$utc, $seo],";
            $data['ppc'] .= "[$utc, $ppc],";
        }

        return View::make('reports.delta', $data);
    }

    /**
     * The repeat customer and adwords spend report
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

        $dailyTotals = $this->iterateOverDateRangeRepeat($data);

        /* Instantiate all the things */
        $data['trends'] = array();

        $data['dates'] = $data['total'] =  $data['repeat'] = $data['referral'] = $data['error'] =
        $data['revenue'] = $data['spend'] = $data['chartdata'] = $data['search'] =
        $data['trends']['search'] = $data['trends']['repeat'] = $data['trends']['referral'] = '';

        $days = $absoluteSearchTotal = $absoluteRepeatTotal = $absoluteReferralTotal = 0;

        foreach ($dailyTotals as $day => $metrics) {
            $days++;
            $total = count($metrics['total']['t']) + count($metrics['total']['m']);
            $repeat = count($metrics['repeat']['t']) + count($metrics['repeat']['m']);
            $referral = count($metrics['referral']['t']) + count($metrics['referral']['m']);
            $search = count($metrics['search']['t']) + count($metrics['search']['m']);

            /* Work order revenue */
            $revenue = $this->getRevenueForDate($day);
            $combinedRevenue = $revenue['t']->Revenue + $revenue['m']->Revenue;
            if ($total && $combinedRevenue) {
                $dividedRevenue = round($combinedRevenue / $total, 2);
            }

            $search = $total - $repeat - $referral;
            $error = 0;
            // $error = $total - $search - $repeat - $referral;

            /* Absolute totals */
            $absoluteSearchTotal = $search + $absoluteSearchTotal;
            $absoluteRepeatTotal = $repeat + $absoluteRepeatTotal;
            $absoluteReferralTotal = $referral + $absoluteReferralTotal;

            /* Adwords spend */
            $adwordsPerCustomer = 0;
            $adwordsCost = $this->getAdwordsCostForDate($day);

            if ($search) {
                $adwordsPerCustomer = $adwordsCost / $search;
                $adwordsPerCustomer = round($adwordsPerCustomer, 2);
            }

            /* Trending averages */
            $trendSearch = round($absoluteSearchTotal / $days, 1);
            $trendRepeat = round($absoluteRepeatTotal / $days, 1);
            $trendReferral = round($absoluteReferralTotal / $days, 1);

            $data['dates'] .= "'$day', ";
            $data['total'] .= "$total, ";
            $data['search'] .= "$search, ";
            $data['repeat'] .= "$repeat, ";
            $data['referral'] .= "$referral, ";
            $data['error'] .= "$error, ";
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
     * The landing page impressions report
     */
    public function showLandingPageReport()
    {
        $start = Input::get('start');
        $end = Input::get('end');
        $data['mode'] = Input::get('metrics');
        $data['chartmode'] = Input::get('chartmode');

        $data['dates'] = '';
        $data['metrics'] = array();

        # Smartphones
        if (Input::get('iphone')) { $data['metrics']['iphone'] = ''; }
        if (Input::get('samsung')) { $data['metrics']['samsung'] = ''; }
        if (Input::get('blackberry')) { $data['metrics']['blackberry'] = ''; }
        if (Input::get('htc')) { $data['metrics']['htc'] = ''; }
        if (Input::get('motorola')) { $data['metrics']['motorola'] = ''; }
        if (Input::get('lg')) { $data['metrics']['lg'] = ''; }
        if (Input::get('nokia')) { $data['metrics']['nokia'] = ''; }
        if (Input::get('ipod')) { $data['metrics']['ipod'] = ''; }
        if (Input::get('sony')) { $data['metrics']['sony'] = ''; }

        # Laptops
        if (Input::get('macbook')) { $data['metrics']['macbook'] = ''; }
        if (Input::get('acer')) { $data['metrics']['acer'] = ''; }
        if (Input::get('dell')) { $data['metrics']['dell'] = ''; }
        if (Input::get('gateway')) { $data['metrics']['gateway'] = ''; }
        if (Input::get('lenovo')) { $data['metrics']['lenovo'] = ''; }
        if (Input::get('asus')) { $data['metrics']['asus'] = ''; }
        if (Input::get('hpcompaq')) { $data['metrics']['hpcompaq'] = ''; }
        if (Input::get('sony')) { $data['metrics']['sony'] = ''; }
        if (Input::get('toshiba')) { $data['metrics']['toshiba'] = ''; }
        if (Input::get('lg')) { $data['metrics']['lg'] = ''; }
        if (Input::get('msi')) { $data['metrics']['msi'] = ''; }
        if (Input::get('fujitsu')) { $data['metrics']['fujitsu'] = ''; }

        # Tablets
        if (Input::get('ipad')) { $data['metrics']['ipad'] = ''; }
        if (Input::get('kindle')) { $data['metrics']['kindle'] = ''; }
        if (Input::get('surface')) { $data['metrics']['surface'] = ''; }
        if (Input::get('asus')) { $data['metrics']['asus'] = ''; }
        if (Input::get('blackberry')) { $data['metrics']['blackberry'] = ''; }
        if (Input::get('samsung')) { $data['metrics']['samsung'] = ''; }

        # Consoles
        if (Input::get('playstation')) { $data['metrics']['playstation'] = ''; }
        if (Input::get('xbox')) { $data['metrics']['xbox'] = ''; }

        # Promotions and Other
        if (Input::get('home')) { $data['metrics']['home'] = ''; }
        if (Input::get('promotions')) { $data['metrics']['promotions'] = ''; }
        if (Input::get('locations')) { $data['metrics']['locations'] = ''; }

        foreach ($data['metrics'] as $key => $value) {
            $data['metrics'][$key]['seo'] = '';
            $data['metrics'][$key]['ppc'] = '';
        }

        $data['start'] = $start ? $start : date('Y-m-d', strtotime("30 days ago"));
        $data['end'] = $end ? $end : date('Y-m-d');

        $today = new DateTime();

        if ($end > $today) {
            $data['end'] = date('Y-m-d');
        }

        $begin = new DateTime($data['start']);
        $end = new DateTime($data['end']);

        // $end->add(new DateInterval('P1D'));

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        // Populate the data

        foreach ($period as $day) {
            $date = $day->format("Y-m-d");
            $data['dates'] .= "'$date', ";

            foreach ($data['metrics'] as $key => $value) {
                if (Input::get('smartphones')) {
                    $smartphones = $this->getLandingPageMetricsForDayTypeBrand('smartphones', $key, $date);
                    $keyname = $key;

                    $data['metrics'][$keyname]['seo'] .= $smartphones['seo'].',';
                    $data['metrics'][$keyname]['ppc'] .= $smartphones['ppc'].',';
                }
                if (Input::get('laptops')) {
                    $laptops = $this->getLandingPageMetricsForDayTypeBrand('laptops', $key, $date);
                    $keyname = $key;

                    $data['metrics'][$keyname]['seo'] .= $laptops['seo'].',';
                    $data['metrics'][$keyname]['ppc'] .= $laptops['ppc'].',';
                }
                if (Input::get('tablets')) {
                    $tablets = $this->getLandingPageMetricsForDayTypeBrand('tablets', $key, $date);
                    $keyname = $key;

                    $data['metrics'][$keyname]['seo'] .= $tablets['seo'].',';
                    $data['metrics'][$keyname]['ppc'] .= $tablets['ppc'].',';
                }
                if (Input::get('home')) {
                    $home = $this->getLandingPageMetricsForDayCategory('home', $date);
                    $keyname = $key;

                    $data['metrics'][$keyname]['seo'] .= $home['seo'].',';
                    $data['metrics'][$keyname]['ppc'] .= $home['ppc'].',';
                }
                if (Input::get('promotions')) {
                    $promotions = $this->getLandingPageMetricsForDayCategory('promotions', $date);
                    $keyname = $key;

                    $data['metrics'][$keyname]['seo'] .= $promotions['seo'].',';
                    $data['metrics'][$keyname]['ppc'] .= $promotions['ppc'].',';
                }
                if (Input::get('locations')) {
                    $locations = $this->getLandingPageMetricsForDayCategory('locations', $date);
                    $keyname = $key;

                    $data['metrics'][$keyname]['seo'] .= $locations['seo'].',';
                    $data['metrics'][$keyname]['ppc'] .= $locations['ppc'].',';

                }
                if (Input::get('playstation')) {
                    $playstation = $this->getLandingPageMetricsForDayBrand('playstation', $date);
                    $keyname = $key;

                    $data['metrics'][$keyname]['seo'] .= $playstation['seo'].',';
                    $data['metrics'][$keyname]['ppc'] .= $playstation['ppc'].',';
                }
                if (Input::get('xbox')) {
                    $playstation = $this->getLandingPageMetricsForDayBrand('xbox', $date);
                    $keyname = $key;

                    $data['metrics'][$keyname]['seo'] .= $playstation['seo'].',';
                    $data['metrics'][$keyname]['ppc'] .= $playstation['ppc'].',';
                }
            }
        }

        return View::make('reports.landing', $data);
    }

/**
MATH FUNCTIONS
 */

    protected function getAverages($absoluteSearchTotal, $absoluteRepeatTotal,
                                   $absoluteReferralTotal, $days, $start, $end)
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

/**
DATE RANGE FUNCTIONS
 */

    protected function iterateOverDateRangeRepeat($dates)
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
            $rangeOutput[$date]['search'] = $this->getSearchCustomersForDay($day);
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

    protected function getSearchCustomersForMonth($month, $year)
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

    protected function getSearchCustomersForDay($day)
    {
        $start = $day->format("Y-m-d 00:00:00.000");
        $end = $day->format("Y-m-d 23:59:59.000");
        $queryResults = $this->searchCustomerQueryRange($start, $end);
        return $queryResults;
    }

/**
DATABASE QUERIES - REPEATS AND ADWORDS REPORT
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
                AND Time >= '$startDate'
                AND Time <= '$endDate'

                ORDER BY CustomerID";

        $results['m'] = DB::connection('mssql-squareone')->select($query);
        $results['t'] = DB::connection('mssql-toronto')->select($query);
        // $results['t'] = array();

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
                AND Time >= '$startDate'
                AND Time <= '$endDate'
                ORDER BY CustomerID";

        $results['m'] = DB::connection('mssql-squareone')->select($query);
        $results['t'] = DB::connection('mssql-toronto')->select($query);
        // $results['t'] = array();

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
                AND Time >= '$startDate'
                AND Time <= '$endDate'
                ORDER BY CustomerID";

        $results['m'] = DB::connection('mssql-squareone')->select($query);
        $results['t'] = DB::connection('mssql-toronto')->select($query);
        // $results['t'] = array();

        $results = $this->addLocation($results);

        return $results;
    }

    protected function searchCustomerQueryRange($startDate, $endDate)
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
                    SELECT OrderID From OrderEntry WHERE Comment LIKE '%CSRH%'
                )
                AND ID NOT IN
                (
                    SELECT OrderEntryID FROM OrderRework
                )
                AND Time >= '$startDate'
                AND Time <= '$endDate'
                ORDER BY CustomerID";

        $results['m'] = DB::connection('mssql-squareone')->select($query);
        $results['t'] = DB::connection('mssql-toronto')->select($query);
        // $results['t'] = array();

        $results = $this->addLocation($results);

        return $results;
    }

    protected function getRevenueForDate($date)
    {
        $services = '3133, 3134, 3033, 3203, 3197, 3195, 3167, 3168, 3141, 3135,
                     3173, 3172, 3136, 3201, 3140, 3137, 3200, 7430, 7432, 7752,
                     7603, 7601, 7599, 1111';

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
        $query = "SELECT sum(cost)/1000000 AS sum FROM adword_performance_hour
                  WHERE DATE='$date'";

        $results = DB::connection('mysql')->select($query)[0]->sum;

        return $results;
    }

    protected function getAdwordsRangeAverage($from, $to)
    {
        $query = "SELECT sum(cost)/1000000 AS sum FROM adword_performance_hour
                  WHERE DATE BETWEEN '$from' AND '$to'";

        $results = DB::connection('mysql')->select($query)[0]->sum;

        return $results;
    }

/**
DATABASE - LANDING PAGES
 */

    protected function getLandingPageMetricsForDayCategory($category, $date)
    {
        $query = "SELECT SUM(organic) AS organic,
                  SUM(cpc) AS cpc
                  FROM google_analytics_page_cache
                  WHERE category = '$category'
                  AND date = '$date'";

        $result = DB::connection('mysql')->select($query)[0];

        if (!isset($result->organic)) {
            $return['seo'] = 0;
            $return['ppc'] = 0;
        } else {
            $return['seo'] = $result->organic;
            $return['ppc'] = $result->cpc;
        }

        return $return;
    }

    protected function getLandingPageMetricsForDayBrand($brand, $date)
    {
        $query = "SELECT SUM(organic) AS organic,
                  SUM(cpc) AS cpc
                  FROM google_analytics_page_cache
                  WHERE brand = '$brand'
                  AND date = '$date'";

        $result = DB::connection('mysql')->select($query)[0];

        if (!isset($result->organic)) {
            $return['seo'] = 0;
            $return['ppc'] = 0;
        } else {
            $return['seo'] = $result->organic;
            $return['ppc'] = $result->cpc;
        }

        return $return;
    }

    protected function getLandingPageMetricsForDayType($type, $date)
    {
        $query = "SELECT SUM(organic) AS organic,
                         SUM(cpc) AS cpc
                  FROM google_analytics_page_cache
                  WHERE type = '$type'
                  AND date = '$date'";

        $result = DB::connection('mysql')->select($query)[0];

        if (!isset($result->organic)) {
            $return['seo'] = 0;
            $return['ppc'] = 0;
        } else {
            $return['seo'] = $result->organic;
            $return['ppc'] = $result->cpc;
        }

        return $return;
    }

    protected function getLandingPageMetricsForDayTypeBrand($type, $brand, $date)
    {
        $query = "SELECT SUM(organic) AS organic,
                         SUM(cpc) AS cpc
                  FROM google_analytics_page_cache
                  WHERE type = '$type'
                  AND brand = '$brand'
                  AND date = '$date'";

        $result = DB::connection('mysql')->select($query)[0];

        if (!isset($result->organic)) {
            $return['seo'] = 0;
            $return['ppc'] = 0;
        } else {
            $return['seo'] = $result->organic;
            $return['ppc'] = $result->cpc;
        }

        return $return;
    }


    protected function getDelta()
    {
        $query = 'SELECT UNIX_TIMESTAMP(date) AS `utc`,
                         organic,
                         cpc,
                         referral,
                         none,
                         date
                  FROM google_analytics_cache ORDER BY date ASC';

        $result = DB::connection('mysql')->select($query);

        return $result;
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

/**
CUSTOMER LOOKUP
 */

    public function getOrderStatuses()
    {
        $query = 'SELECT * FROM OrderStatus';
        $results = DB::connection('mssql-squareone')->select($query);
        $status = [];

        foreach ($results as $result) {
            $id = $result->ID;
            $name = $result->Name;
            $status[$id] = $name;
        }

        return $status;
    }

    public function customerLookup()
    {
        $number = Input::get('number');

        $query = "SELECT [Order].Time, OrderEntry.LastUpdated, OrderStatus.Name, OrderEntry.Description, OrderEntry.Comment
                  FROM [Order], OrderEntry, Customer, OrderManagement, OrderStatus
                  WHERE [Order].CustomerID = Customer.ID
                  AND OrderEntry.OrderID = [Order].ID
                  AND OrderManagement.OrderEntryID = OrderEntry.ID
                  AND OrderStatus.ID = OrderManagement.StatusID
                  AND REPLACE(REPLACE(Customer.PhoneNumber,' ',''),'-','') = '$number'";

        $results['m'] = DB::connection('mssql-squareone')->select($query);
        $results['t'] = DB::connection('mssql-toronto')->select($query);

        $data['records'] = array_merge($results['m'],$results['t']);

        return View::make('records.customerlookup', $data);
    }
}