#!/usr/local/bin/php -q
<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;

function read_env() {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required(['API_TOKEN', 'GROUP_ID', 'HITOBITO_API_BASE_URL', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_ENCRYPTION', 'CYON_DOMAIN']);
}

function read_list_id() {
    $listName = $_ENV['RECIPIENT'];
    if (!$listName) return null;

    $sanitizedListName = preg_replace('/[^A-Za-z0-9_.]/', '_', $listName);
    return $_ENV["LIST_ID_{$sanitizedListName}"] ?? null;
}

function read_message($inputFile) {
    // Separate the first line from the rest of the mail, it contains metainformation about who sent the email
    $firstLine = '';
    if (!feof($inputFile)) $firstLine = fgets($inputFile, 1024);

    $message = '';
    while (!feof($inputFile)) {
        $message .= fgets($inputFile, 1024);
    }
    return [$message, $firstLine];
}

function fetch_mailing_list_subscribers($hitobitoBaseUrl, $groupId, $listId, $accessToken) {
    $client = new Client(['base_uri' => $hitobitoBaseUrl]);
    $response = $client->request('GET', 'api/mailing_lists/' . $listId, ['query' => ['extra_fields[mailing_lists]' => 'subscribers', 'token' => $accessToken]]);
    if ($response->getStatusCode() !== 200) return [];
    $data = json_decode($response->getBody());
    $subscribers = $data->data->attributes->subscribers;
    return array_values(array_unique(array_filter(array_merge([], ...array_map(function($subscriber) { return $subscriber->list_emails; }, $subscribers)))));
}

function send_message($message, $firstLine, $recipients) {
    if (count($recipients) === 0) return;
    $recipientAddresses = array_map(function($recipient) { return new Address($recipient); }, $recipients);

    $sender = new Address($_ENV['MAIL_USERNAME']);
    $envelope = new Envelope($sender, $recipientAddresses);

    $message = new RawMessage($message);
    $transport = new Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport($_ENV['MAIL_HOST'], $_ENV['MAIL_PORT']);
    $transport->setLocalDomain($_ENV['CYON_DOMAIN']);
    $transport->setUsername($_ENV['MAIL_USERNAME']);
    $transport->setPassword($_ENV['MAIL_PASSWORD']);
    $mailer = new Mailer($transport);
    $mailer->send($message, $envelope);
}

ini_set('error_reporting', 0);

read_env();
$listId = read_list_id();

if ($listId) {
    $stdin = fopen('php://stdin', 'r');
    //$outfile = fopen(__DIR__ . '/mail.txt', 'a');
    //fwrite($outfile, "Mail received for list ${_ENV['RECIPIENT']} with id $listId\n");
    [$message, $firstLine] = read_message($stdin);
    //fwrite($outfile, $firstLine);
    //fwrite($outfile, $message);

    $recipients = fetch_mailing_list_subscribers($_ENV['HITOBITO_API_BASE_URL'], $_ENV['GROUP_ID'], $listId, $_ENV['API_TOKEN']);

    send_message($message, $firstLine, $recipients);

    fclose($stdin);
    //fclose($outfile);
}
