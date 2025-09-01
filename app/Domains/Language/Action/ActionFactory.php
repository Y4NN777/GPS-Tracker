<?php declare(strict_types=1);

namespace App\Domains\Language\Action;

use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\Language\Model\Language as Model;

class ActionFactory extends ActionFactoryAbstract
{
    /**
     * @var ?\App\Domains\Language\Model\Language
     */
    protected ?Model $row;

    /**
     * Initialize and set the current application language.
     *
     * @return void
     */
    public function set(): void
    {
        $this->actionHandle(Set::class);
    }

    /**
     * Apply language from the current request (middleware usage).
     *
     * @return void
     */
    public function request(): void
    {
        $this->actionHandle(Request::class);
    }
}
