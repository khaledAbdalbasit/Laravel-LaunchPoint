<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use LaunchPoint\Traits\CanDisplayLogo;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ListCommand
 *
 * Artisan command to display an elegant table of all LaunchPoint commands and options.
 */
class ListCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available LaunchPoint commands with their options';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->displayLogo();

        $commands = $this->getApplication()->all('launchpoint');
        ksort($commands);

        $rows = [];

        foreach ($commands as $name => $cmd) {
            $options = [];
            foreach ($cmd->getDefinition()->getOptions() as $opt) {
                // Skip global symphony options
                if (in_array($opt->getName(), ['help', 'quiet', 'verbose', 'version', 'ansi', 'no-ansi', 'no-interaction', 'env'])) {
                    continue;
                }
                $shortcut = $opt->getShortcut() ? "-{$opt->getShortcut()}, " : '';
                $valueRequired = $opt->isValueRequired() ? '=' : '';
                $options[] = "{$shortcut}--{$opt->getName()}{$valueRequired}";
            }

            $optionsString = empty($options) ? '<fg=gray>none</>' : implode(', ', $options);

            $rows[] = [
                "<fg=cyan;options=bold>{$name}</>",
                $cmd->getDescription(),
                $optionsString,
            ];
        }

        $this->components->info('Available LaunchPoint Commands');
        $this->table(['Command', 'Description', 'Options'], $rows);

        return self::SUCCESS;
    }
}
