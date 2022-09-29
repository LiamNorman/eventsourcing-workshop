<?php

namespace Workshop\Domains\Wallet\Upcasters;

use EventSauce\EventSourcing\Upcasting\Upcaster;

class TransactedAtUpcaster implements Upcaster
{
    public function upcast(array $message): array
    {
        if (isset($message['headers']['__time_of_recording'])) {
            $message['payload']['transacted_at'] = $message['headers']['__time_of_recording'];
        }

        return $message;
    }
}