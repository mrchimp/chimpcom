<?php 
/**
 * Show musical scales
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;

/**
 * Show musical scales
 */
class Scale extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $notes = ['C','C#','D','D#','E','F','F#','G','G#','A','A#','B'];
        
        $scales = [
            'Major' => [1,3,5,6,8,10,12], 
            'Minor' => [1,3,4,6,8,9,11], 
            'Harmonic Minor' => [1,3,4,6,7,8,9,12], 
            'Melodic Minor Asc' => [1,3,4,6,8,10,12], 
            'Melodic Minor Desc' => [1,3,4,6,8,9,11], 
            'Diminished' => [1,3,4,6,7,9,10,12], 
            'Whole Tone' => [1,3,5,7,9,11], 
            'Spanish 8 Tone' => [1,2,4,5,6,7,9,11], 
            'Flamenco' => [1,2,4,5,6,8,9,11], 
            'Inverted Diminished' => [1,2,4,5,7,8,10,11], 
            'Major Locrian' => [1,3,5,6,7,9,11], 
            'Mixolydian' => [1,3,5,6,8,10,11],
            'Gypsy' => [1,3,4,7,8,9,12,13,15,16,19,20,21,24,25,27,28],
            'Arabian' => [1,3,4,6,7,9,10,12,13,15,16,18,19,21,22,24,25,27],
            'Persian' => [1,2,5,6,7,9,12,13,14,17,18,20,22,25,26,27],
            'Byzantine' => [1,2,5,6,8,9,12,13,14,17,18,20,21,25,26,27],
            'Oriental' => [1,2,5,6,7,10,11,13,14,17,18,19,22,23,25,26],
            'Japanese' => [1,3,6,8,9,13,15,18,20,21,25,27],
            'Indian (ascending)' => [1,2,6,8,9,13,14,18,20,21,25,26],
            'Indian (descending)' => [1,2,4,6,8,9,11,13,14,16,18,20,21,23,25,26,28],
            'Romanian' => [1,3,4,7,8,10,11],
            'Jewish' => [1,2,5,6,8,9,11,13,14,17,18,20,21,23,25,26]
        ];
        
        $root = $this->input->get(1);
        if (!$root) {
            $root = 'C';
        }
        
        $root_num = array_search($root, $notes) - 1;
        $this->response->title("Root note: $root <br>");
        $scale = $this->input->get(2);
        
        if ($scale) { // show one scale
            $this->response->title("Scale: $scale<br>");
        
            foreach ($scales[$scale] as $note) {
                $this->response->say($notes[($note+$root_num)%12] . ', ');
            }
        } else { // show all scales
            foreach ($scales as $scale_name => $scale_notes) {
                $this->response->title("$scale_name <br>");
                foreach ($scales[$scale_name] as $note) {
                    $this->response->say($notes[($note+$root_num)%12] . ', ');
                }
                $this->response->say('<br>');
            }
        }
    }

}