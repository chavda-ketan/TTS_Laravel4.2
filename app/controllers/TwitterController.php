<?php

class TwitterController extends BaseController {

    public function index() {
        $data['content'] = $this->doTwitterSearch();

        return View::make('twitter.tweets', $data);
    }

    public function doTwitterSearch() {
        $mCoords = '43.589045,-79.644119,50km';
        $tCoords = '43.652527,-79.381961,50km';
        $searchResultsString = '';
        $resultsArray = [];
        $url = urlencode('http://techknowspace.com/contact-us/contact-info/');

        $queries = $this->getSearchQueries();

        foreach ($queries as $query) {
            $qid = $query->id;
            $request['q'] = $query->query;
            $request['geocode'] = $mCoords;

            $searchResults = Twitter::getSearch($request);

            $searchResultsString .= '<pre class="twitterquery"><h2>'.$request['q'].' - <a href="/twitter/delete?id='.$qid.'">x</a></h2>';

            foreach ($searchResults->statuses as $result) {
                $userFullName = $result->user->name;
                $userHandle = $result->user->screen_name;

                $id = $result->id;
                $tweetText = $result->text;
                $tweetDate = $result->created_at;

                $searchResultsString .= "<p>$tweetDate - @$userHandle - $tweetText - ";
                $searchResultsString .= "<a class='twitter-reply-button'
                                          data-count='none'
                                          href='https://twitter.com/intent/tweet?in_reply_to=$id&url=$url'>Tweet to @$userHandle</a></p>";
            }

            $searchResultsString .= '</pre>';
        }


        return $searchResultsString;
    }

    public function showLatLongForGeoSearch() {
        $torontoGeocode = $this->getGeocodeForLocation('Toronto');
        $mississaugaGeocode = $this->getGeocodeForLocation('Mississauga');

        $mergedResults['toronto'] = $torontoGeocode;
        $mergedResults['mississauga'] = $mississaugaGeocode;

        return dd($mergedResults);
    }

    public function addSearchQuery(){
        $query = Input::get('query');

        DB::connection('mysql')->insert('INSERT INTO twitter_queries (query)
                                         VALUES (?)', array($query));

        return $this->index();
    }

    public function removeSearchQuery(){
        $diarrhea = Input::get('id');

        DB::connection('mysql')->delete('DELETE FROM twitter_queries
                                         WHERE id = ?', array($diarrhea));

        return $this->index();
    }

    public function getSearchQueries(){
        $queries = DB::connection('mysql')->table('twitter_queries')->get();
        return $queries;
    }

    public function getGeocodeForLocation($city) {
        $query['query'] = $city;
        $query['granularity'] = 'city';

        $geoSearchResults = Twitter::getGeoSearch($query);

        return $geoSearchResults;
    }

}