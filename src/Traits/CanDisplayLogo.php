<?php

namespace LaunchPoint\Traits;

trait CanDisplayLogo
{
    /**
     * Display the LaunchPoint logo and header.
     * Each line uses a single color tag to preserve correct terminal spacing.
     *
     * @return void
     */
    protected function displayLogo()
    {
        $this->line('<fg=red>           !</>');
        $this->line('<fg=red>           ^</>');
        $this->line('<fg=cyan>          / \</>');
        $this->line('<fg=cyan>         /===\</>');
        $this->line('<fg=cyan>        | LP  |</>   <fg=cyan;options=bold>LaunchPoint</>');
        $this->line('<fg=cyan>        |     |</>   <fg=white>────────────</>');
        $this->line('<fg=cyan>        |_____|</>   <fg=gray>Starter Kit</>');
        $this->line('<fg=red>          / \</>');
        $this->line('<fg=yellow>         V   V</>');
        $this->line('<fg=yellow>        ( ( ) )</>');
        $this->line('<fg=gray>         (  )  )</>');
        $this->newLine();
    }

    /**
     * Static method for composer hooks or manual calls.
     * Uses ANSI escape codes for cross-environment color support.
     */
    public static function displayWelcomeMessage()
    {
        $cyan   = "\033[36m";
        $red    = "\033[31m";
        $yellow = "\033[33m";
        $gray   = "\033[90m";
        $white  = "\033[37m";
        $bold   = "\033[1m";
        $reset  = "\033[0m";

        echo "{$red}           !{$reset}\n";
        echo "{$red}           ^{$reset}\n";
        echo "{$cyan}          / \\{$reset}\n";
        echo "{$cyan}         /===\\{$reset}\n";
        echo "{$cyan}        | LP  |{$reset}   {$cyan}{$bold}LaunchPoint{$reset}\n";
        echo "{$cyan}        |     |{$reset}   {$white}────────────{$reset}\n";
        echo "{$cyan}        |_____|{$reset}   {$gray}Starter Kit{$reset}\n";
        echo "{$red}          / \\{$reset}\n";
        echo "{$yellow}         V   V{$reset}\n";
        echo "{$yellow}        ( ( ) ){$reset}\n";
        echo "{$gray}         (  )  ){$reset}\n";
        echo PHP_EOL;
    }
}
