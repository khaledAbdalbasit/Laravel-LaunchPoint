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
        |  L  |  LaunchPoint
        |  P  |  ───────────
        |_____|  Starter Kit
         / V \  
        V     V  
</>
EOT;
        $this->line($logo);
        $this->newLine();
    }
}
