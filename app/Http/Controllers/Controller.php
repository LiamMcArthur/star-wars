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
        while ($this->status !== 417) {

            if ($this->path->count() > 0) {
                $initialPath = $this->path->implode('');
            } else {
                $initialPath = 'f';
            }

            $status = $this->navigateDroid($initialPath);

            $path = $this->path->implode('');

            if ($status !== 410) {

                $left = $this->navigateDroid($path . 'l');

                if ($left !== 410) {

                    $right = $this->navigateDroid($path . 'f');

                    if ($right !== 410) {

                        $this->navigateDroid($path . 'f');

                    }

                }

            }

            $this->navigateDroid('f');

        }

    }

    private function navigateDroid($direction)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://deathstar.victoriaplum.com/empire.php?name=mariusz&path=' . $direction,
        ]);

        curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // GONE - navigating
        if ($statusCode === 410) {
            $this->status = 410;
            $this->path->push($direction);
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

        dump($this->path);

        return $statusCode;

    }

}
