<?php declare(strict_types=1);

namespace App\Domains\Health\Command;

use PDO;use Throwable;use Illuminate\Support\Facades\DB;use App\Domains\Core\Command\CommandAbstract;

class Check extends CommandAbstract
{
    /**
     * @var string
     */
    protected $signature = 'health:check {--details}';

    /**
     * @var string
     */
    protected $description = 'Environment health check: PHP extensions, DB, cache/queue basics';

    public function handle(): int
    {
        $ok = true;

        $this->info('Health check starting');

        // PHP extensions
        $exts = [
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'redis' => extension_loaded('redis'),
        ];

        foreach ($exts as $ext => $loaded) {
            if ($loaded) {
                $this->info("ext:$ext = OK");
            } else {
                $ok = false;
                if ($ext === 'redis') {
                    $this->error('ext:redis = MISSING (ok if REDIS_CLIENT=predis)');
                } else {
                    $this->error("ext:$ext = MISSING");
                }
            }
        }

        // DB connection
        try {
            // Force a simple query
            DB::select('select 1 as ok');
            $this->info('db:connection = OK');
        } catch (Throwable $e) {
            $ok = false;
            $this->error('db:connection = FAIL');
            $this->line('Hint: install php8.2-mysql then restart your SAPI (php-fpm/apache)');
            if ($this->option('details')) {
                $this->line($e->getMessage());
            }
        }

        // Cache driver
        $cache = config('cache.default');
        $this->info('cache:driver = '.$cache);

        // Queue connection
        $queue = config('queue.default');
        $this->info('queue:connection = '.$queue);

        $this->info('Health check finished');

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
