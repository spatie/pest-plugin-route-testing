<?php

declare(strict_types=1);

namespace Spatie\RouteTesting;

use Pest\Contracts\Plugins\AddsOutput;

/**
 * @internal
 */
final class Plugin implements AddsOutput
{
    public function addOutput(int $exitCode): int
    {
        $this->output->writeln(sprintf(
            '  <fg=gray>Memory:</>   <fg=default>%s MB</>',
            round(memory_get_usage(true) / 1000 ** 2, 3)
        ));

        return $exitCode;
    }
}
