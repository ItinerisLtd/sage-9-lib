<?php

declare(strict_types=1);

namespace Roots\Sage;

use Illuminate\Container\Container as BaseContainer;

use function register_shutdown_function;

class Container extends BaseContainer
{
    protected array $terminatingCallbacks = [];

    public function terminating(callable $callback): static
    {
        if (empty($this->terminatingCallbacks)) {
            register_shutdown_function([$this, 'terminate']);
        }

        $this->terminatingCallbacks[] = $callback;

        return $this;
    }

    public function terminate(): void
    {
        foreach ($this->terminatingCallbacks as $callback) {
            $callback();
        }

        $this->terminatingCallbacks = [];
    }
}
