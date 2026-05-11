<?php
// Lightweight warmup — no Laravel boot needed.
// Vercel executes this as a serverless PHP function.
// Hit every 5 min via UptimeRobot to prevent cold starts.
header('Content-Type: application/json');
http_response_code(200);
echo json_encode(['ok' => true, 'ts' => time(), 'region' => getenv('VERCEL_REGION') ?: 'sin1']);
