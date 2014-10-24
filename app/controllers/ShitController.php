<?php

class ShitController extends BaseController
{

    /* binding for shit on the shitty shit */

    public function toggleTrack()
    {
        $id = Input::get('id');

        DB::connection('mysql')->update('UPDATE campaign_group SET track = NOT track
                                         WHERE id = ?', array($id));

        return 'success';
    }

    /* turds */
    public function rangeAdd()
    {
        $poop = Input::get('id');
        $start = Input::get('start');
        $end = Input::get('end');

        if (!$end) {
            $end = '0000-00-00';
        }

        DB::connection('mysql')->insert('INSERT INTO campaign_group_type_custom (group_id, d_f, d_t)
                                         VALUES (?, ?, ?)', array($poop, $start, $end));

        return 'success';
    }

    /* flush */
    public function rangeDel()
    {
        $diarrhea = Input::get('id');

        DB::connection('mysql')->delete('DELETE FROM campaign_group_type_custom
                                         WHERE id = ?', array($diarrhea));

        return 'success';
    }

    /* weewee poopoo mudbutt */
    public function showRangeDailyData()
    {
        $id = Input::get('id');
        $rms = Input::get('rms');
        $start = Input::get('start');
        $end = Input::get('end');

        $startDateTime = new DateTime( $start );
        $endDateTime = new DateTime( $end );
        $endDateTime = $endDateTime->modify( '+1 day' );

        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDateTime, $interval, $endDateTime);

        $data = $data['dates'] = array();

        foreach($dateRange as $date){
            $day = $date->format("Y-m-d");
            $data['dates'][$day] = $this->getAdwordsData($day, $day, $rms, $id);
        }

        $data['dates'] = array_reverse($data['dates']);

        return View::make('snippets.adwordsrange', $data);
        // return var_dump($data);
    }

    protected function getAdwordsData($date_f,$date_t,$rms,$group)
    {
        $campaigns = '';

        $campaignResult = DB::connection('mysql')->select("SELECT group_id, campaign_id
                                         FROM campaign_group_type INNER JOIN campaign_group_item
                                         ON campaign_group_type.id = campaign_group_item.type
                                         WHERE campaign_group_type.group_id = ?", array($group));

        foreach($campaignResult as $row)
        {
            $id = $row->campaign_id;
            $campaigns .= "$id,";
        }

        $campaigns = rtrim($campaigns, ',');

        // RMS Data
        $query = "SELECT (sum(wo)+sum(wo_a)) AS wo,
                (sum(sale)+sum(sale_a)) as sale from adword_r_c_p_rms
                where name='$rms' and date between '$date_f' and '$date_t'";

        $rms = DB::connection('mysql')->select($query)[0];

        // Adwords Data
        $adwordsQuery = "SELECT sum(cost) as cost,
                        (sum(cost)/sum(clicks)/1000000) as cpc,
                        (sum(clicks)/sum(impressions)*100) as ctr,
                        sum(clicks) as clicks, avg(avgPosition) as pos,
                        sum(impressions) imp, avg(qualityScore) as qs
                        FROM adword_keyword where campaignID in ($campaigns)";

        $adwordsQueryFragment = "and device!=2 and date between '$date_f' and '$date_t'";
        $adwordsMobileQueryFragment = "and device=2 and date between '$date_f' and '$date_t'";

        $adword = DB::connection('mysql')->select($adwordsQuery.$adwordsQueryFragment)[0];
        $adword_m = DB::connection('mysql')->select($adwordsQuery.$adwordsMobileQueryFragment)[0];

        $date1 = new DateTime($date_f);
        $date2 = new DateTime($date_t);

        // $dd=$date2->diff($date1)->format("%a");
        // $dd=$dd+1;
        $dd = 1;

        $adwordsData = array();

        // All
        $adwordsData['pos'] = number_format($adword->pos, 1);
        $adwordsData['clicks'] = number_format($adword->clicks / $dd, 2);
        $adwordsData['ctr'] = number_format($adword->ctr, 2);
        $adwordsData['cpc'] = number_format($adword->cpc, 2);
        $adwordsData['qs'] = number_format($adword->qs, 1);
        $adwordsData['imp'] = number_format($adword->imp / $dd);
        $adwordsData['cost'] = number_format($adword->cost / $dd / 1000000);

        // Mobile
        $adwordsData['pos_m'] = number_format($adword_m->pos, 1);
        $adwordsData['clicks_m'] = number_format($adword_m->clicks/$dd, 2);
        $adwordsData['ctr_m'] = number_format($adword_m->ctr, 2);
        $adwordsData['cpc_m'] = number_format($adword_m->cpc, 2);
        $adwordsData['qs_m'] = number_format($adword_m->qs, 1);
        $adwordsData['imp_m'] = number_format($adword_m->imp / $dd);
        $adwordsData['cost_m'] = number_format($adword_m->cost / $dd / 1000000);
        $adwordsData['rms_wo'] = number_format($rms->wo / $dd, 2);
        $adwordsData['rms_sale'] = number_format($rms->sale / $dd);
        $adwordsData['campaigns'] = $campaigns;

        return $adwordsData;
    }
}
