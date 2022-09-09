<?php

namespace Workshop\Domains\Wallet\Tests;

use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Exceptions\InsufficientTokensException;
use Workshop\Domains\Wallet\Wallet;

class WithdrawTokensTest extends WalletTestCase
{
    /** @test */
    public function it_cannot_withdraw_tokens_if_not_available()
    {
        $this->given()
            ->when(fn(Wallet $wallet) => $wallet->withdraw(100))
            ->expectToFail(InsufficientTokensException::insufficientTokens(100, 0));
    }

    /** @test */
    public function it_can_withdraw_tokens_if_available()
    {
        $this->given(new TokensDeposited(200))
            ->when(fn(Wallet $wallet) => $wallet->withdraw(100))
            ->then(new TokensWithdrawn(100));
    }
}
