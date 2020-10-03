<?php

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show musical scales
 */
class Scale extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('scale');
        $this->setDescription('Show musical scales.');
        $this->setHelp('All black notes are written as sharps.');
        $this->addArgument(
            'root',
            null,
            'The root note of the scale. Defaults to C.'
        );
        $this->addArgument(
            'scale',
            null,
            'The type of scale to show. If not set, all scales will be shown.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $notes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];

        $scales = [
            'major' => [1, 3, 5, 6, 8, 10, 12],
            'minor' => [1, 3, 4, 6, 8, 9, 11],
            'harmonic minor' => [1, 3, 4, 6, 7, 8, 9, 12],
            'melodic minor asc' => [1, 3, 4, 6, 8, 10, 12],
            'melodic minor desc' => [1, 3, 4, 6, 8, 9, 11],
            'diminished' => [1, 3, 4, 6, 7, 9, 10, 12],
            'whole tone' => [1, 3, 5, 7, 9, 11],
            'spanish 8 tone' => [1, 2, 4, 5, 6, 7, 9, 11],
            'flamenco' => [1, 2, 4, 5, 6, 8, 9, 11],
            'inverted diminished' => [1, 2, 4, 5, 7, 8, 10, 11],
            'major locrian' => [1, 3, 5, 6, 7, 9, 11],
            'mixolydian' => [1, 3, 5, 6, 8, 10, 11],
            'gypsy' => [1, 3, 4, 7, 8, 9, 12, 13, 15, 16, 19, 20, 21, 24, 25, 27, 28],
            'arabian' => [1, 3, 4, 6, 7, 9, 10, 12, 13, 15, 16, 18, 19, 21, 22, 24, 25, 27],
            'persian' => [1, 2, 5, 6, 7, 9, 12, 13, 14, 17, 18, 20, 22, 25, 26, 27],
            'byzantine' => [1, 2, 5, 6, 8, 9, 12, 13, 14, 17, 18, 20, 21, 25, 26, 27],
            'oriental' => [1, 2, 5, 6, 7, 10, 11, 13, 14, 17, 18, 19, 22, 23, 25, 26],
            'japanese' => [1, 3, 6, 8, 9, 13, 15, 18, 20, 21, 25, 27],
            'indian (ascending)' => [1, 2, 6, 8, 9, 13, 14, 18, 20, 21, 25, 26],
            'indian (descending)' => [1, 2, 4, 6, 8, 9, 11, 13, 14, 16, 18, 20, 21, 23, 25, 26, 28],
            'romanian' => [1, 3, 4, 7, 8, 10, 11],
            'jewish' => [1, 2, 5, 6, 8, 9, 11, 13, 14, 17, 18, 20, 21, 23, 25, 26]
        ];

        $root = strtoupper($input->getArgument('root'));
        if (!$root) {
            $root = 'C';
        }
        $scale = strtolower($input->getArgument('scale'));

        $root_num = array_search($root, $notes) - 1;
        $output->title("Root note: $root <br>");

        if ($scale && isset($scales[$scale])) { // show one scale
            $output->title("Scale: $scale<br>");

            foreach ($scales[$scale] as $note) {
                $output->say($notes[($note + $root_num) % 12] . ', ');
            }
        } else { // show all scales
            foreach ($scales as $scale_name => $scale_notes) {
                $output->title("$scale_name <br>");
                foreach ($scales[$scale_name] as $note) {
                    $output->say($notes[($note + $root_num) % 12] . ', ');
                }
                $output->say('<br>');
            }
        }

        return 0;
    }
}
