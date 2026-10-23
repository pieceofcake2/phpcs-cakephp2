<?php
namespace App\Console\Command;

use App\Model\Post;

/**
 * Report Shell
 *
 * @property Post $Post
 */
class ReportShell extends AppShell
{
    /**
     * Models used by the shell
     *
     * @var array
     */
    public $uses = ['Post'];

    /**
     * Main
     *
     * @return void
     */
    public function main()
    {
        $count = 0;
        while ($row = $this->Post->find('first', ['offset' => $count])) {
            $this->out($row['Post']['title']);
            $count++;
        }
    }

    /**
     * Subcommand with an underscored name
     *
     * @return void
     */
    public function send_mail()
    {
        $this->out('sent');
    }
}
