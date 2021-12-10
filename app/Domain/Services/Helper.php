<?php


namespace App\Domain\Services;


class Helper
{
    public static function viewer_cmp($a, $b) {
        return strcmp($a->viewer_count, $b->viewer_count);
    }

    public static function date_cmp($a, $b) {
        $v1 = strtotime($a->started_at);
        $v2 = strtotime($b->started_at);

        return strcmp($v1, $v2);
    }
}