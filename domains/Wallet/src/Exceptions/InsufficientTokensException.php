<?php

namespace Workshop\Domains\Wallet\Exceptions;

class InsufficientTokensException extends \Exception
{

    public static function insufficientTokens(int $withdrawnTokens, int $availableTokens): static
    {
        return new self("Can't withdraw {$withdrawnTokens} tokens when only {$availableTokens} available.");
    }
}
