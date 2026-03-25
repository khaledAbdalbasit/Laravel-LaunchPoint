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
<fg=red;options=bold>           !</>
<fg=red;options=bold>           ^</>
<fg=cyan>          / \ </>
<fg=cyan>         /===\</>
<fg=cyan>        |  <fg=white;options=bold>L</>  |</>   <fg=cyan;options=bold>LaunchPoint</>
<fg=cyan>        |  <fg=white;options=bold>P</>  |</>   <fg=white>───────────</>
<fg=cyan>        |_____|</>   <fg=gray>Starter Kit</>
<fg=red>         / V \ </>
<fg=yellow>        V     V</>
<fg=yellow;options=blink>       (  ( )  )</>
<fg=gray>        ( ( ) )</>
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
{$red}{$bold}           !{$reset}
{$red}{$bold}           ^{$reset}
{$cyan}          / \ {$reset}
{$cyan}         /===\{$reset}
{$cyan}        |  {$white}{$bold}L{$reset}{$cyan}  |{$reset}   {$cyan}{$bold}LaunchPoint{$reset}
{$cyan}        |  {$white}{$bold}P{$reset}{$cyan}  |{$reset}   {$white}───────────{$reset}
{$cyan}        |_____|{$reset}   {$gray}Starter Kit{$reset}
{$red}         / V \ {$reset}
{$yellow}        V     V{$reset}
{$yellow}       (  ( )  ){$reset}
{$gray}        ( ( ) ){$reset}
";
        echo $logo . PHP_EOL;
    }
}
