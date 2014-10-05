<?php

class RecordsController extends BaseController {

	public function index() {
		return $queryFirstDay . ' - ' . $queryLastDay;
	}

/**
PAGE FUNCTIONS
 */

	public function newRepeatReport() {
		$m = Input::get('m');
		$y = Input::get('y');

		$month = $m ? $m : date('F');
		$year = $y ? $y : date('Y');

		$start = strtotime('today - 30 days');

	}

	/* Display the repeat customers for a given month */
	public function showMonthlyRepeat() {
		$m = Input::get('m');
		$y = Input::get('y');

		$month = $m ? $m : date('F');
		$year = $y ? $y : date('Y');

        $data = $this->tallyMonth($month, $year);

        $dates = $this->monthDateRange($month, $year);

        $dailyTotals = $this->iterateOverDateRange($dates);


        $data['dates'] = '';
        $data['total'] = '';
        $data['repeat'] = '';
        $data['referral'] = '';
        $data['chartdata'] = '';


        foreach ($dailyTotals as $day => $metrics) {
            $total = count($metrics['total']['t']) + count($metrics['total']['m']);
            $repeat = count($metrics['repeat']['t']) + count($metrics['repeat']['m']);
            $referral = count($metrics['referral']['t']) + count($metrics['referral']['m']);

            $data['dates'] .= "'$day', ";
            $data['total'] .= "'$total', ";
            $data['repeat'] .= "'$repeat', ";
            $data['referral'] .= "'$referral', ";

            $data['chartdata'] .= "['$day', '$total', '$repeat', '$referral'], ";
        }


        $data['debugme'] = $dailyTotals;

		$data['month'] = $month;
		$data['year'] = $year;

        $data['showpicker'] = 1;

		return View::make('reports.repeatcustomers', $data);
	}

/**
 RECORD MATH FUNCTIONS
 */

private function tallyMonth($month, $year)
{

    $total = $this->getTotalCustomersForMonth($month, $year);
    $repeats = $this->getRepeatCustomersForMonth($month, $year);
    $referrals = $this->getReferralCustomersForMonth($month, $year);

    $data['torontoTotalCount'] = count($total['t']);
    $data['mississaugaTotalCount'] = count($total['m']);

    $data['torontoRepeatCount'] = count($repeats['t']);
    $data['mississaugaRepeatCount'] = count($repeats['m']);

    $data['torontoReferralCount'] = count($referrals['t']);
    $data['mississaugaReferralCount'] = count($referrals['m']);

    $data['torontoPercentage'] = $this->percentage($data['torontoRepeatCount'], $data['torontoTotalCount'], 2);
    $data['mississaugaPercentage'] = $this->percentage($data['mississaugaRepeatCount'], $data['mississaugaTotalCount'], 2);

    $data['torontoReferralPercentage'] = $this->percentage($data['torontoReferralCount'], $data['torontoTotalCount'], 2);
    $data['mississaugaReferralPercentage'] = $this->percentage($data['mississaugaReferralCount'], $data['mississaugaTotalCount'], 2);

    $data['combinedTotalCount'] = $data['torontoTotalCount']+$data['mississaugaTotalCount'];
    $data['combinedRepeatCount'] = $data['torontoRepeatCount']+$data['mississaugaRepeatCount'];
    $data['combinedReferralCount'] = $data['torontoReferralCount']+$data['mississaugaReferralCount'];

    $data['combinedPercentage'] = $this->percentage($data['combinedRepeatCount'], $data['combinedTotalCount'], 2);
    $data['combinedReferralPercentage'] = $this->percentage($data['combinedReferralCount'], $data['combinedTotalCount'], 2);

    return $data;
}

/**
 DATE RANGE FUNCTIONS
 */

	protected function iterateOverDateRange($dates) {
		$begin = new DateTime($dates['first']);
		$end = new DateTime($dates['last']);

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
	protected function monthDateRange($month, $year) {
		$firstDay = date('Y-m-d 00:00:00.000', strtotime("first day of $month $year"));
		$lastDay = date('Y-m-d 23:59:59.000', strtotime("last day of $month $year"));

		$return['first'] = $firstDay;
		$return['last'] = $lastDay;

		return $return;
	}

	/* Previous function but single day */
	protected function singleDay($day, $year) {
		$firstDay = date('Y-m-d 00:00:00.000', strtotime("first day of $month $year"));
		$lastDay = date('Y-m-d 23:59:59.000', strtotime("last day of $month $year"));

		$return['start'] = $dayEnd;
		$return['end'] = $dayStart;

		return $return;
	}

	/* Month */
	protected function getTotalCustomersForMonth($month, $year) {
		$dates = $this->monthDateRange($month, $year);
		$queryResults = $this->totalCustomerQueryRange($dates['first'], $dates['last']);
		return $queryResults;
	}

	protected function getRepeatCustomersForMonth($month, $year) {
		$dates = $this->monthDateRange($month, $year);
		$queryResults = $this->repeatCustomerQueryRange($dates['first'], $dates['last']);
		return $queryResults;
	}

	protected function getReferralCustomersForMonth($month, $year) {
		$dates = $this->monthDateRange($month, $year);
		$queryResults = $this->referralCustomerQueryRange($dates['first'], $dates['last']);
		return $queryResults;
	}

	/* Day */
	protected function getTotalCustomersForDay($day) {
		$start = $day->format("Y-m-d 00:00:00.000");
		$end = $day->format("Y-m-d 23:59:59.000");
		$queryResults = $this->totalCustomerQueryRange($start, $end);
		return $queryResults;
	}

	protected function getRepeatCustomersForDay($day) {
		$start = $day->format("Y-m-d 00:00:00.000");
		$end = $day->format("Y-m-d 23:59:59.000");
		$queryResults = $this->repeatCustomerQueryRange($start, $end);
		return $queryResults;
	}

	protected function getReferralCustomersForDay($day) {
		$start = $day->format("Y-m-d 00:00:00.000");
		$end = $day->format("Y-m-d 23:59:59.000");
		$queryResults = $this->referralCustomerQueryRange($start, $end);
		return $queryResults;
	}

/**
DATABASE QUERIES
 */

	/**
	 * Query a date range for all customers in that span
	 * @param  string $startDate [description]
	 * @param  string $endDate   [description]
	 * @return array            [description]
	 */
	protected function totalCustomerQueryRange($startDate, $endDate) {
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

	protected function repeatCustomerQueryRange($startDate, $endDate) {
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

	protected function referralCustomerQueryRange($startDate, $endDate) {
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

/**
UTILITY FUNCTIONS
 */

	/* Add location tag to supplied array of results */
	public function addLocation($results) {
		foreach ($results['m'] as $result) {
			$result->Location = 'm';
		}

		foreach ($results['t'] as $result) {
			$result->Location = 't';
		}

		return $results;
	}

	/* Calculate percentage of values, with decimal precision */
	public function percentage($val1, $val2, $precision) {
		$res = round(($val1 / $val2) * 100, $precision);
		return $res;
	}
}