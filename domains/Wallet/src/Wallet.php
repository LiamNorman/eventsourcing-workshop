<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Exceptions\InsufficientTokensException;

class Wallet implements AggregateRoot
{
    private int $availableTokens = 0;

    use AggregateRootBehaviour;

    public function deposit(int $tokens)
    {
        $this->recordThat(new TokensDeposited($tokens));
    }

    public function withdraw(int $tokens)
    {
        if ($tokens > $this->availableTokens) {
            throw InsufficientTokensException::insufficientTokens($tokens, $this->availableTokens);
        }

        $this->recordThat(new TokensWithdrawn($tokens));
    }

    public function applyTokensWithdrawn(TokensWithdrawn $event): void
    {
        $this->availableTokens -= $event->tokens;
    }

    private function applyTokensDeposited(TokensDeposited $event): void
    {
        $this->availableTokens += $event->tokens;
    }
}
