<?php

class Zume_Query {

    public static function candidate_hero( $requested_range ) {
        $stats = [
            'current_timestamp' => time(),
            'requested_range' => $requested_range,
        ];
        $stats[] = [
            'key' => 'registrations',
            'label' => 'Registrations',
            'description' => 'Registrations to all Zume properties.',
            'value' => 0,
            'goal' => 0,
            'trend' => 0,
            'category' => 'candidate',
            'type' => 'number',
            'public' => true,
        ];
        return $stats;
    }

}
