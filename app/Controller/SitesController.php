<?php

namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

class SitesController
{
    public function selectSites(Request $request, Response $response, array $args)
    {
        # funkcja wczytująca cały plik z API Linkhouse.co do ciągu znaków
        $str = file_get_contents('https://app.linkhouse.co/rekrutacja/strony');
        # funkcja dekodująca ciąg JSON
        $myarray = json_decode($str, true);
        $records = [];

        $main_page = $myarray['requested_site'];

        foreach($myarray['sites'] as $site)
        {
            if ($site['site'] == $main_page)
            {
                $sum_z1 = $site['traffic'] + $site['quality'] + $site['price'];
                $w1_1 = $site['traffic'] / $sum_z1;
                $w1_2 = $site['quality'] / $sum_z1;
                $w1_3 = $site['price'] / $sum_z1;
            }
        }

        foreach($myarray['sites'] as $pss_json)
        {
            $sum_z2 = $pss_json['traffic'] + $pss_json['quality'] + $pss_json['price'];
            $w2_1 = $pss_json['traffic'] / $sum_z2;
            $w2_2 = $pss_json['quality'] / $sum_z2;
            $w2_3 = $pss_json['price'] / $sum_z2;

            $min1 = min($w1_1, $w2_1);
            $min2 = min($w1_2, $w2_2);
            $min3 = min($w1_3, $w2_3);

            $wp = round((($min1 + $min2 + $min3)*100), 5);  

            $site = $pss_json['site'];
            $traffic = $pss_json['traffic'];
            $quality = $pss_json['quality'];
            $price = $pss_json['price'];

            array_push($records, array($site, $wp, $traffic, $quality, $price));
        }

        array_multisort(array_column($records, 1), SORT_DESC, $records);

        $best_records = [];
        $i = 0;
        foreach($records as  $i => $record)
        {
            array_push($best_records, array("site" => $record[0], "similarity" => $record[1], "traffic" => $record[2], "quality" => $record[3], "price" => $record[4]));
            if ($i++ > 9) break;
        }
        return $response->withJson($best_records,200);
    }
}