<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\WithdrawalFailed;
use Workshop\Domains\Wallet\Exceptions\InsufficientTokensException;

class Wallet implements AggregateRoot
{
    private int $availableTokens = 0;

    use AggregateRootBehaviour;

    public function deposit(int $tokens, string $description = 'Unknown')
    {
        $this->recordThat(new TokensDeposited($tokens, $description));
    }

    public function withdraw(int $tokens, string $description = 'Unknown')
    {
        if ($tokens > $this->availableTokens) {
            $this->recordThat(new WithdrawalFailed());
            throw InsufficientTokensException::insufficientTokens($tokens, $this->availableTokens);
        }

        $this->recordThat(new TokensWithdrawn($tokens, $description));
    }

    public function applyTokensWithdrawn(TokensWithdrawn $event): void
    {
        $this->availableTokens -= $event->tokens;
    }

    private function applyTokensDeposited(TokensDeposited $event): void
    {
        $this->availableTokens += $event->tokens;
    }

    private function applyWithdrawalFailed(): void
    {
        return;
    }
}
