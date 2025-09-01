<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Throwable;
use App\Domains\Core\Traits\Factory;

class App extends ServiceProvider
{
    use Factory;

    /**
     * @return void
     */
    public function boot(): void
    {
        // Make application boot resilient when DB driver/connection is unavailable
        try {
            $this->configuration();
        } catch (Throwable $e) {
            logger()->error($e);
            logger()->warning('App configuration binding skipped due to database error. Hint: install php8.2-mysql and ensure MySQL is running.');
        }

        try {
            $this->language();
        } catch (Throwable $e) {
            logger()->error($e);
            logger()->warning('Language initialization skipped due to database error.');
        }
    }

    /**
     * @return void
     */
    protected function configuration(): void
    {
        $this->factory('Configuration')->action()->appBind();
    }

    /**
     * @return void
     */
    protected function language(): void
    {
        $this->factory('Language')->action()->set();
    }
}
