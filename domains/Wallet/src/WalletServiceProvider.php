<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\MessageDecoratorChain;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\ObjectMapperPayloadSerializer;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\UuidEncoding\BinaryUuidEncoder;
use EventSauce\UuidEncoding\StringUuidEncoder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\WithdrawalFailed;
use Workshop\Domains\Wallet\Infra\Decorators\RandomNumberDecorator;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Infra\WalletRepository;

class WalletServiceProvider extends ServiceProvider
{
    public function register()
    {
        $explicitlyMappedClassNameInflector = new \EventSauce\EventSourcing\ExplicitlyMappedClassNameInflector([
            // Map event types to a specified event type
            TokensDeposited::class => 'tokens_deposited',
            TokensWithdrawn::class => 'tokens_withdrawn',
            WithdrawalFailed::class => 'withdrawal_failed',
            Wallet::class => 'wallet',
            WalletId::class => 'wallet_id',
        ]);

        $this->app->bind(WalletMessageRepository::class, function (Application $application) use ($explicitlyMappedClassNameInflector) {
            return new WalletMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_messages',
                serializer: new ConstructingMessageSerializer(classNameInflector: $explicitlyMappedClassNameInflector, payloadSerializer: new ObjectMapperPayloadSerializer(),),
                tableSchema: new DefaultTableSchema(),
                uuidEncoder: new StringUuidEncoder(),
            );
        });

        $this->app->bind(WalletRepository::class, function () use ($explicitlyMappedClassNameInflector) {
            return new WalletRepository(
                $this->app->make(WalletMessageRepository::class),
                new MessageDispatcherChain(),
                new MessageDecoratorChain(
                    new DefaultHeadersDecorator(inflector: $explicitlyMappedClassNameInflector),
                    new RandomNumberDecorator()
                ),
                $explicitlyMappedClassNameInflector,
            );
        });
    }
}
