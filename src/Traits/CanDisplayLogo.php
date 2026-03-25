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
       / \
      /   \      LaunchPoint
     /_____\     ───────────
     |     |     Starter Kit
     |_____|
</>
EOT;
        $this->line($logo);
        $this->newLine();
    }
}
