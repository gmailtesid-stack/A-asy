<?php
// Ultra-lightweight warmup endpoint — no Laravel bootstrap needed.
// Call this via a cron/UptimeRobot every 5 min to keep the function warm.
header('Content-Type: application/json');
echo json_encode(['ok' => true, 'ts' => time()]);
