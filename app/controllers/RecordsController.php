<?php

class RecordsController extends BaseController {

	public function index() {
		return $queryFirstDay . ' - ' . $queryLastDay;
	}

	/*
	PAGE FUNCTIONS
	 */
	public function showWeeklyRepeat() {
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
	public function showMonthlyRepeat() {
		$m = Input::get('m');
		$y = Input::get('y');

		$month = $m ? $m : date('F');
		$year = $y ? $y : date('Y');

		$total = $this->getTotalCustomersForMonth($month, $year);
		$repeats = $this->getRepeatCustomersForMonth($month, $year);
		$referrals = $this->getReferralCustomersForMonth($month, $year);

		$torontoTotalCount = count($total['t']);
		$mississaugaTotalCount = count($total['m']);

		$torontoRepeatCount = count($repeats['t']);
		$mississaugaRepeatCount = count($repeats['m']);

		$torontoReferralCount = count($referrals['t']);
		$mississaugaReferralCount = count($referrals['m']);

		$torontoPercentage = $this->percentage($torontoRepeatCount, $torontoTotalCount, 2);
		$mississaugaPercentage = $this->percentage($mississaugaRepeatCount, $mississaugaTotalCount, 2);

		$torontoReferralPercentage = $this->percentage($torontoReferralCount, $torontoTotalCount, 2);
		$mississaugaReferralPercentage = $this->percentage($mississaugaReferralCount, $mississaugaTotalCount, 2);

		$combinedTotalCount = $torontoTotalCount + $mississaugaTotalCount;
		$combinedRepeatCount = $torontoRepeatCount + $mississaugaRepeatCount;
		$combinedReferralCount = $torontoReferralCount + $mississaugaReferralCount;

		$combinedPercentage = $this->percentage($combinedRepeatCount, $combinedTotalCount, 2);
		$combinedReferralPercentage = $this->percentage($combinedReferralCount, $combinedTotalCount, 2);

		$outputString = '<pre>';
		$outputString .= "$month $year<br><br>";

		$outputString .= "SquareOne total customers - $mississaugaTotalCount <br>";
		$outputString .= "SquareOne repeat customers - $mississaugaRepeatCount - $mississaugaPercentage% repeats<br>";
		$outputString .= "SquareOne referral customers - $mississaugaReferralCount - $mississaugaReferralPercentage% referrals<br><br>";

		$outputString .= "Toronto total customers - $torontoTotalCount <br>";
		$outputString .= "Toronto repeat customers - $torontoRepeatCount - $torontoPercentage% repeats <br>";
		$outputString .= "Toronto referral customers - $torontoReferralCount - $torontoReferralPercentage% referrals <br><br>";

		$outputString .= "Total customers - $combinedTotalCount <br>";
		$outputString .= "Repeat customers - $combinedRepeatCount - $combinedPercentage% repeats <br>";
		$outputString .= "Referral customers - $combinedReferralCount - $combinedReferralPercentage% referrals";

		$outputString .= '</pre>';

		$data['showpicker'] = 1;
		$data['daterange'] = '';
		$data['result'] = $outputString;
		$data['graphdata'] = '';

		return View::make('reports.repeatcustomers', $data);
	}

	/* RID CODE OF CRUFT */

	/* Input a month and year, get an array with the first and last calendar date in SQL Server format */
	protected function monthDateRange($month, $year) {
		$firstDay = date('Y-m-d 00:00:00.000', strtotime("first day of $month $year"));
		$lastDay = date('Y-m-d 23:59:59.000', strtotime("last day of $month $year"));

		$return['first'] = $firstDay;
		$return['last'] = $lastDay;

		return $return;
	}

	/* Returns a full month of customer counts */
	protected function getTotalCustomersForMonth($month, $year) {
		$dates = $this->monthDateRange($month, $year);

		$queryResults = $this->totalCustomerQueryRange($dates['first'], $dates['last']);

		return $queryResults;
	}

	/* Returns a full month of customer repeats */
	protected function getRepeatCustomersForMonth($month, $year) {
		$dates = $this->monthDateRange($month, $year);

		$queryResults = $this->repeatCustomerQueryRange($dates['first'], $dates['last']);

		return $queryResults;
	}

	/* Returns a full month of customer referrals */
	protected function getReferralCustomersForMonth($month, $year) {
		$dates = $this->monthDateRange($month, $year);

		$queryResults = $this->referralCustomerQueryRange($dates['first'], $dates['last']);

		return $queryResults;
	}

	/* Detaching query logic so these can be easily invoked for specific dates and ranges */
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