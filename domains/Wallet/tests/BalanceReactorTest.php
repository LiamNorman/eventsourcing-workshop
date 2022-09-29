<?php

namespace Workshop\Domains\Wallet\Tests;

use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Reactors\BalanceReactor;
use Workshop\Domains\Wallet\Wallet;

class BalanceReactorTest extends WalletTestCase
{
    /** @test */
    public function it_can_deposit_tokens()
    {

        $balanceReactor = new BalanceReactor($this->messageDispatcher());
        $this->given()
            ->when(fn(Wallet $wallet) => $wallet->deposit(100))
            ->then(new TokensDeposited(100, 'Unknown'))
            ->then(function () {
                $this->assertTrue($balanceReactor->handle);
            });
    }

    protected function messageDispatcher(): MessageDispatcher
    {
        return new SynchronousMessageDispatcher();
    }
}
