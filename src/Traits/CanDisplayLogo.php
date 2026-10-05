<?php

namespace LaunchPoint\Traits;

trait CanDisplayLogo
{
    /**
     * Display the LaunchPoint logo and header.
     *
     * @return void
     */
    protected function displayLogo()
    {
        $this->line('');
        $this->line('<fg=cyan>  ██╗      ██████╗ </>');
        $this->line('<fg=cyan>  ██║     ██╔══██╗</>');
        $this->line('<fg=cyan>  ██║     ██████╔╝</>  <fg=white;options=bold>LaunchPoint</>');
        $this->line('<fg=cyan>  ██║     ██╔═══╝ </>  <fg=gray>────────────────</>');
        $this->line('<fg=cyan>  ███████╗██║      </>  <fg=gray>Starter Kit</>');
        $this->line('<fg=cyan>  ╚══════╝╚═╝</>');
        $this->line('');
    }

    /**
     * Static method for composer hooks or manual calls.
     * Uses ANSI escape codes for cross-environment color support.
     */
    public static function displayWelcomeMessage()
    {
        $cyan   = "\033[36m";
        $white  = "\033[37m";
        $gray   = "\033[90m";
        $bold   = "\033[1m";
        $reset  = "\033[0m";

        echo "\n";
        echo "{$cyan}  ██╗      ██████╗ {$reset}\n";
        echo "{$cyan}  ██║     ██╔══██╗{$reset}\n";
        echo "{$cyan}  ██║     ██████╔╝{$reset}  {$white}{$bold}LaunchPoint{$reset}\n";
        echo "{$cyan}  ██║     ██╔═══╝ {$reset}  {$gray}────────────────{$reset}\n";
        echo "{$cyan}  ███████╗██║      {$reset}  {$gray}Starter Kit{$reset}\n";
        echo "{$cyan}  ╚══════╝╚═╝{$reset}\n";
        echo "\n";
    }
}
