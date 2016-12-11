<?php
/**
 * Get an answer
 */

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get an answer
 */
class Magiceightball extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('magiceightball');
        $this->setDescription('Get an answer.');
        $this->addOption(
            'force',
            'f',
            null,
            'Use the force.'
        );
    }

    /**
     * Run the command
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('force')) {
            $answers = [
                'It is your destiny.',
                'It is pointless to resist, my son.',
                'I\'ve got a bad feeling about this.',
                'Difficult to see. Always in motion is the future.',
                'Your feelings betray you.',
                'Search your feelings, you know it to be true.',
                'Noooooooooooooooo!'
            ];
        } else {
            $answers = [
                "Don't count on it.",
                "It is decidedly so.",
                "My reply is no.",
                "My sources say no.",
                "Outlook not so good.",
                "Very doubtful.",
                "Reply hazy, try again.",
                "Concentrate and ask again.",
                "Ask again later.",
                "Better not tell you now.",
                "Cannot predict now.",
                "As I see it, Yes.",
                "It is certain.",
                "Most likely.",
                "Outlook good.",
                "Signs point to yes.",
                "Without a doubt.",
                "Yes.",
                "Yes definitely.",
                "You may rely on it.",
                "Probably.",
                "Maybe.",
                "Fuck knows.",
                "Dunno.",
                "Negative.",
                "Indeed.",
                "I doubt it.",
                "Meh.",
                "Doubtful.",
                "I say no.",
                "I say yes."
            ];
        }

        $output->write($answers[mt_rand(0, (count($answers) - 1))]);
    }
}
