<?php

namespace Spatie\RouteTesting\Commands;

use Illuminate\Console\Command;
use function Termwind\{render};

class RenderOutputCommand extends Command
{
    /** @var string $signature */
    protected $signature = 'render {asserted} {total}';

    public function handle(): void
    {
        $asserted = $this->argument('asserted');
        $total = $this->argument('total');

        if (! is_int($asserted) || ! is_int($total)) {
            throw new \InvalidArgumentException('Asserted argument must be an integer.');
        }

        $data = "Tested {$asserted} out of {$total} routes.";

        render(
            sprintf('<div class="px-1 bg-green-300">%s</div>', $data));
    }
}
