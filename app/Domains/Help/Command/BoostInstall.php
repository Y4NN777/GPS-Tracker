<?php declare(strict_types=1);

namespace App\Domains\Help\Command;

use Illuminate\Support\Facades\Artisan as ArtisanFacade;

class BoostInstall extends CommandAbstract
{
    /**
     * @var string
     */
    protected $signature = 'help:boost:install';

    /**
     * @var string
     */
    protected $description = 'Install Laravel Boost (proxy command with environment guidance)';

    /**
     * @return int
     */
    public function handle(): int
    {
        $env = (string)config('app.env');
        $debug = (bool)config('app.debug');

        // If Boost is actually enabled by the provider, just inform the user how to run the real installer.
        // We cannot delegate to the vendor command by name here (it would recurse to us),
        // and instantiating the vendor command directly would bypass its dependency injection prompts.
        // So we give clear instructions based on current environment.
        if (! $debug && ! in_array($env, ['local', 'development'], true)) {
            $this->info('Laravel Boost is installed (dev dependency) but disabled for this environment.');
            $this->line('');
            $this->line('To run the official Boost installer with interactive prompts, enable it temporarily by either:');
            $this->line('  1) Setting APP_ENV=local, or');
            $this->line('  2) Setting APP_DEBUG=true');
            $this->line('');
            $this->line('Examples:');
            $this->line('  APP_ENV=local php artisan boost:install');
            $this->line('  APP_DEBUG=true php artisan boost:install');
            $this->line('');
            $this->line('Why this happens: The package provider only registers Boost commands on local or debug environments.');
            $this->line('This proxy command is provided so the namespace exists and shows you how to enable it safely.');

            return self::SUCCESS;
        }

        // If we are in a permitted environment, check whether the vendor command is registered.
        $commands = collect(ArtisanFacade::all())->keys()->filter(fn ($k) => str_starts_with($k, 'boost:'))->values();

        if ($commands->contains('boost:install')) {
            $this->info('Boost is enabled. Please run the installer using:');
            $this->line('  php artisan boost:install');
            $this->line('');
            $this->line('If you still see this message repeatedly, try clearing caches:');
            $this->line('  php artisan optimize:clear && php artisan package:discover');

            return self::SUCCESS;
        }

        // Fallback if provider didn’t register despite being in permissive env
        $this->error('Boost commands were not registered even though the environment allows them.');
        $this->line('Try: php artisan optimize:clear && php artisan package:discover');
        $this->line('If the issue persists, ensure "laravel/boost" is installed and the service provider is discovered.');

        return self::FAILURE;
    }
}
