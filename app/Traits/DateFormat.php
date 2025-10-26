<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateFormat
{
    public function textFormatDate($date, $isComplete = false): Object
    {
        if (!$date) {
            return (object) [
                'raw'       => null,
                'formatted' => null,
                'human'     => null,
            ];
        }
        $parsed = Carbon::parse($date);

        return (object) [
            'raw'       => $date,
            'formatted' => $isComplete ? $parsed->format('d-m-Y H:i:s') : $parsed->format('d-m-Y'),
            'human'     => $isComplete ? $parsed->translatedFormat('j \d\e F \d\e Y H:i') : $parsed->translatedFormat('j \d\e F \d\e Y'),
        ];
    }
}
