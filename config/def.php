<?php
return [
    'cli_make_task_cmd' => 'cli-task/make-all',
    'cli_send_task_cmd' => 'cli-task/send-task-all-at-one',

    'fc_days_number'    => 5,

    'filter_col' => [
        'dc.ID',
        'dps.Name',
        'dc.CaseProcess'
    ],

    'filter' 		=> [
	   '0' => '=',
	   '1' => 'dc.CaseProcess',
	   '2' => 1
    ]

];
