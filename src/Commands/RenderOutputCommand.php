<?php

namespace Spatie\RouteTesting\Commands;

use Illuminate\Console\Command;
use function Termwind\{render};

class RenderOutputCommand extends Command
{
    /** @var string $signature */
    protected $signature = 'render {asserted} {total} {ignored}';

    public function handle(): void
    {
        $asserted = $this->argument('asserted');
        $total = $this->argument('total');
        $ignored = $this->argument('ignored');

        if (! is_int($asserted) || ! is_int($total)) {
            throw new \InvalidArgumentException('Asserted argument must be an integer.');
        }

        $data = "Tested {$asserted} out of {$total} routes.";

        if ($asserted === $total) {
            render(sprintf('<div class="px-1 bg-green-300">%s</div>', $data));

            return;
        }

        render(sprintf('<div class="px-1 bg-yellow-300">%s</div>', $data));

        if (! is_string($ignored)) {
            throw new \InvalidArgumentException('Asserted argument must be a string.');
        }

        if ($ignored !== '') {
            render(sprintf('<div class="px-1 bg-yellow-300">Ignored bindings: %s</div>', $ignored));
        }
    }
}
