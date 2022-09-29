<?php

namespace Workshop\Domains\Wallet\Reactors;

use Carbon\Carbon;
use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Infra\NotificationService;
use Illuminate\Support\Facades\Cache;
use Workshop\Domains\Wallet\PublicEvents\Balance\BalanceUpdated;

class BalanceReactor extends EventConsumer
{
    public function __construct(
        private readonly MessageDispatcher $messageDispatcher,
    )
    {
    }

    public function handleTokensDeposited(TokensDeposited $event, Message $message): void
    {
        $walletId = $message->aggregateRootId()->toString();

        $balance = 0;

        $this->messageDispatcher->dispatch(new BalanceUpdated(
            balance: $balance,
        ));
    }


    public function handleTokensWithdrawn(TokensWithdrawn $event, Message $message): void
    {
        $walletId = $message->aggregateRootId()->toString();

        $balance = 0;

        $this->messageDispatcher->dispatch(new BalanceUpdated(
            balance: $balance,
        ));
    }

}