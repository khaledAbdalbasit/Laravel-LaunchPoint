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
<fg=cyan;options=bold>
           !
           ^
          / \
         /===\
        |  L  |
        |  P  |
        |_____|  LaunchPoint
         / V \   ───────────
        V     V  Starter Kit
</>
EOT;
        $this->line($logo);
        $this->newLine();
    }
}
