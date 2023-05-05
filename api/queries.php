<?php

class Zume_Query {

    public static function hero( $params ) {
//        $value = 100;
//        $goal = 90;
//        $trend = 110;
//
//        $requested_range = $this->requested_range( $params );
        $stats = [
            'current_timestamp' => time(),
        ];
//        $stats['requested_range'] = $requested_range;
//        $stats['hero'] = [
//            'key' => 'candidate',
//            'label' => 'Candidates',
//            'description' => 'Candidates are visitors',
//            'value' => $value, // current value
//            'goal' => $goal, // value set as goal
//            'goal_color' => $this->get_valence( $value, $goal ),
//            'trend' => $trend, // value from previous block of time
//            'trend_percent' => $this->get_valence( $value, $trend ),
//            'category' => 'candidate',
//        ];
//
//        $stats['facts'][] = [
//            'key' => 'visitors',
//            'label' => 'Visitors',
//            'description' => 'Visitors to all Zume properties.',
//            'value' => 0,
//            'goal' => 0,
//            'trend' => 0,
//            'category' => 'candidate',
//        ];
//        $stats['facts'][] = [
//            'key' => 'registrations',
//            'label' => 'Registrations',
//            'description' => 'Registrations to all Zume properties.',
//            'value' => 0,
//            'goal' => 0,
//            'trend' => 0,
//            'category' => 'candidate',
//        ];

        return $stats;

    }
}
