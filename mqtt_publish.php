<?php

require __DIR__ . '/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

function publishMqtt(string $topic, string $message): bool {
    $server   = 'broker.hivemq.com';
    $port     = 1883;
    $clientId = 'php-publisher-' . uniqid();

    try {
        $mqtt = new MqttClient($server, $port, $clientId);
        $mqtt->connect((new ConnectionSettings())->setConnectTimeout(10));
        $mqtt->publish($topic, $message, 0, false);
        $mqtt->disconnect();
        return true;
    } catch (Exception $e) {
        error_log("MQTT publish error: " . $e->getMessage());
        return false;
    }
}