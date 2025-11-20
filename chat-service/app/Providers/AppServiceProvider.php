<?php
declare(strict_types=1);

namespace App\Providers;

use App\Http\Persistence\DBAL\MessageRepository;
use App\Message\Domain\Repository\MessageRepositoryInterface;
use App\Message\TransformerDTO\TransformerDto;
use App\Shared\Application\InterfaceDto\TransformerToDtoInterface;
use App\Shared\Domain\Event\EventBusInterface;
use App\Shared\Domain\Event\LaravelEventBus;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(EventBusInterface::class, LaravelEventBus::class);

        $this->app->bind(
            MessageRepositoryInterface::class,
            MessageRepository::class
        );

        $this->app->bind(
            TransformerToDtoInterface::class,
            TransformerDto::class
        );

        $this->app->singleton(Connection::class, function () {
            return DriverManager::getConnection([
                'dbname'   => env('DB_DATABASE', 'messages'),
                'user'     => env('DB_USERNAME', 'chatuser'),
                'password' => env('DB_PASSWORD', 'chatpass'),
                'host'     => env('DB_HOST', 'messages-db'),
                'port'     => env('DB_PORT', 5432),
                'driver'   => 'pdo_pgsql',
            ]);
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(static function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
