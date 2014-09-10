<?php

class TwitterController extends BaseController {

	public function index()
	{
		$mCoords = '43.589045,-79.644119,5km';
		$tCoords = '43.652527,-79.381961,5km';

		$query['q'] = 'broken phone';
		$query['geocode'] = '43.652527,-79.381961,5km';

		$searchResults = Twitter::getSearch($query);

		$searchResultsString = '<pre>';

		foreach ($searchResults->statuses as $result) {
			$userFullName = $result->user->name;
			$userHandle = $result->user->screen_name;
			$userLocation = $result->user->location;

			$tweetText = $result->text;
			$tweetDate = $result->created_at;

			$searchResultsString .= "@$userHandle - $userLocation - $tweetDate - $tweetText <br>";
			// $searchResultsString .= print_r($result);
		}

		$searchResultsString .= '</pre>';

		return $searchResultsString;
	}

	public function doTwitterSearch()
	{
		
	}

	public function showLatLongForGeoSearch()
	{
		$torontoGeocode = $this->getGeocodeForLocation('Toronto');
		$mississaugaGeocode = $this->getGeocodeForLocation('Mississauga');

		$mergedResults['toronto'] = $torontoGeocode;
		$mergedResults['mississauga'] = $mississaugaGeocode;

		return dd($mergedResults);
	}

	public function getGeocodeForLocation($city)
	{
		$query['query'] = $city;
		$query['granularity'] = 'city';

		$geoSearchResults = Twitter::getGeoSearch($query);

		return $geoSearchResults;
	}

}