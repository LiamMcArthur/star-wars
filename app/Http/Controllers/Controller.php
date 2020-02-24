<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->path = collect();
        $this->moves = 0;
        $this->status = null;
    }

    public function defendBlueprints()
    {
        // TODO Stop using random order. Try go forward first and only use left/ right if forward fails.
        $status = $this->status;
        $path = $this->path->implode('');

        while ($status !== 200) {
            $forwards = $this->navigateDroid($path . 'f');
            if ($forwards !== 410) {
                break;
            }
        }

        while ($status !== 200) {
            $forwards = $this->navigateDroid($path . 'l');
            if ($forwards !== 410) {
                break;
            }
        }

        while ($status !== 200) {
            $forwards = $this->navigateDroid($path . 'r');
            if ($forwards !== 410) {
                break;
            }
        }

    }

    private function navigateDroid($direction)
    {
        if ($this->path->count() > 0) {
            $path = $this->path->implode('');
        } else {
            $path = $direction;
        }

        dump($path);


        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://deathstar.victoriaplum.com/empire.php?name=mariusz&path=' . $path,
        ]);

        curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // GONE - navigating
        if ($statusCode === 410) {
            $this->status = 410;
            $this->path->push($path . $direction);
        }

        // CRASHED - empty the path and try again
        if ($statusCode === 417) {
            $this->status = 417;
            $this->path->pop();
        }

        // SUCCESS - we reached our destination
        if ($statusCode === 200) {
//            $this->status = 200;
//            dd("Success", $generatedPath);
        }

        return $statusCode;

    }

}
