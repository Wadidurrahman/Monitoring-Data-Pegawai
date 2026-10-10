
<?php

return [
    'path' => trim(env('MONITORING_PATH', 'monitoring-private'), '/'),

    'allowed_ips' => array_values(array_filter(
        array_map(
            'trim',
            explode(',', (string) env('MONITORING_ALLOWED_IPS', ''))
        )
    )),

    'export_enabled' => env('MONITORING_EXPORT_ENABLED', false),

    'access_hours' => (int) env('MONITORING_ACCESS_HOURS', 5),
];
