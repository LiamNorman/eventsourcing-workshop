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

        if ($message['headers']['__event_type'] == 'tokens_deposited') {
            $data = [
                'corrections' => [
                    'b8d0b0e0-5c1a-4b1e-8c7c-1c6b1b1b1b1b' => 10,
                ],
            ];

            $corrections = $data['corrections'];

            foreach ($corrections as $transactionId => $tokenCorrection) {
                if ($message['headers']['__event_id'] === $transactionId) {
                    $message['payload']['tokens'] = $tokenCorrection;
                }
            }
        }


        return $message;
    }
}