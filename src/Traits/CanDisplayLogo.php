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
     /  o  \     ───────────
    /_______\    Starter Kit
      |   |
      |___|
</>
EOT;
        $this->line($logo);
        $this->newLine();
    }

    /**
     * Static method for composer hooks or manual calls.
     */
    public static function displayWelcomeMessage()
    {
        $logo = "
       / \
      /   \      LaunchPoint
     /  o  \     ───────────
    /_______\    Starter Kit
      |   |
      |___|
";
        echo $logo . PHP_EOL;
    }
}
