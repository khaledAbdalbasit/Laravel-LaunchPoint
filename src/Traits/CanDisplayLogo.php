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
        $logo = <<<EOT
<fg=red;options=bold>    __    __  </>
<fg=red;options=bold>   /  \  /  \ </>
<fg=cyan>  /  LP  \   <fg=cyan;options=bold>LaunchPoint</>
<fg=cyan> /  ______\  <fg=white>──────────────</>
<fg=cyan>|  |        <fg=gray>Starter Kit</>
<fg=cyan>|  |______</>
<fg=cyan> \        /</>
<fg=cyan>  \______/</>
EOT;
        $this->line($logo);
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

        $logo = "
{$red}{$bold}    __    __  {$reset}
{$red}{$bold}   /  \  /  \ {$reset}
{$cyan}  /  LP  \   {$cyan}{$bold}LaunchPoint{$reset}
{$cyan} /  ______\  {$white}──────────────{$reset}
{$cyan}|  |        {$gray}Starter Kit{$reset}
{$cyan}|  |______{$reset}
{$cyan} \        /{$reset}
{$cyan}  \______/{$reset}
";
        echo $logo . PHP_EOL;
    }
}
