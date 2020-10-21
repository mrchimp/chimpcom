<?php

namespace Tests\Unit\Console;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Mrchimp\Chimpcom\Console\Output;
use Tests\TestCase;

class OutputTest extends TestCase
{
    /** @test */
    public function write_appends_message_to_output()
    {
        $output = new Output();

        $output->write('First');
        $output->write('Second');

        $this->assertEquals('FirstSecond', $output->getTextOutput());
    }

    /** @test */
    public function say_does_the_same_as_write()
    {
        $output = new Output();

        $output->say('First');
        $output->say('Second');

        $this->assertEquals('FirstSecond', $output->getTextOutput());
    }

    /** @test */
    public function say_can_append_a_newline()
    {
        $output = new Output();

        $output->write('First', true);
        $output->write('Second');

        $this->assertEquals('First<br>Second', $output->getTextOutput());
    }

    /** @test */
    public function user_details_can_be_populated()
    {
        $output = new Output();

        $output->populateUserDetails();
        $array = $output->toArray();

        $this->assertEquals('Guest', $array['user']['name']);
        $this->assertEquals(-1, $array['user']['id']);

        $this->markTestIncomplete('This doesn\'t test getting details from Auth.');
    }

    /** @test */
    public function getUserDetails_does_the_same_as_populateUserDetails_but_more_deprecatedly()
    {
        $output = new Output();

        $output->getUserDetails();
        $array = $output->toArray();

        $this->assertEquals('Guest', $array['user']['name']);
        $this->assertEquals(-1, $array['user']['id']);

        $this->markTestIncomplete('This doesn\'t test getting details from Auth.');
    }

    /** @test */
    public function error_appends_text_wrapped_in_red_highlight()
    {
        $output = new Output();

        $output->error('error');

        $this->assertEquals('<span class="red_highlight">error</span>', $output->getTextOutput());
    }

    /** @test */
    public function alert_appends_text_wrapped_in_green_highlight()
    {
        $output = new Output();

        $output->alert('alert');

        $this->assertEquals('<span class="green_highlight">alert</span>', $output->getTextOutput());
    }

    /** @test */
    public function grey_appends_text_wrapped_in_grey_styled_html()
    {
        $output = new Output();

        $output->grey('grey');

        $this->assertEquals('<span class="grey_text">grey</span>', $output->getTextOutput());
    }

    /** @test */
    public function title_appends_text_wrapped_in_blue_highlight()
    {
        $output = new Output();

        $output->title('title');

        $this->assertEquals('<span class="blue_highlight">title</span>', $output->getTextOutput());
    }

    /** @test */
    public function getJsonResponse_returns_a_json_response()
    {
        $output = new Output();
        $output->write('Hello!');

        $response = $output->getJsonResponse();
        $data = $response->getData();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('Hello!', $data->cmd_out);
    }

    /** @test */
    public function getTextOutput_gets_the_output_text()
    {
        $output = new Output();

        $output->write('Hello!');

        $this->assertEquals('Hello!', $output->getTextOutput());
    }

    /** @test */
    public function usePasswordInput()
    {
        $output = new Output();

        $this->assertFalse($output->toArray()['show_pass']);

        $output->usePasswordInput();

        $this->assertTrue($output->toArray()['show_pass']);
    }

    /** @test */
    public function log_outputs_to_the_log()
    {
        $output = new Output();

        $output->log('Log this!');

        $this->assertEquals('Log this!', $output->toArray()['log']);
    }

    /** @test */
    public function setCmdOut_overwrites_the_output_text()
    {
        $output = new Output();

        $output->write('This will go away.');

        $output->setCmdOut('This will replace it.');

        $this->assertEquals(
            'This will replace it.',
            $output->toArray()['cmd_out']
        );
    }

    /** @test */
    public function cFill_populates_cmd_fill()
    {
        $output = new Output();

        $output->cFill('Output this');

        $this->assertEquals(
            'Output this',
            $output->toArray()['cmd_fill']
        );
    }

    /** @test */
    public function redirect_makes_it_redirect()
    {
        $output = new Output();

        $output->redirect('https://example.com');

        $this->assertEquals(
            'https://example.com',
            $output->toArray()['redirect']
        );
    }

    /** @test */
    public function openWindow_opens_a_window_probably()
    {
        $output = new Output();

        $output->openWindow(
            'https://example.com',
            'window specs here'
        );

        $this->assertEquals('https://example.com', $output->toArray()['openWindow']);
        $this->assertEquals('window specs here', $output->toArray()['openWindowSpecs']);
    }

    /** @test */
    public function resetTerminal_sets_normal_input_type()
    {
        $output = new Output();

        $output->resetTerminal();

        $this->assertFalse($output->toArray()['show_pass']);
    }

    /** @test */
    public function validator_errors_can_be_written_to_output()
    {
        $output = new Output();
        $validator = Validator::make([], ['name' => 'required']);

        $output->writeErrors($validator, 'Something went horribly wrong');
        $text = $output->getTextOutput();

        $this->assertStringContainsString('Something went horribly wrong', $text);
        $this->assertStringContainsString('The name field is required', $text);
    }

    /** @test */
    public function http_status_code_can_be_set_and_retrieved()
    {
        $output = new Output();

        $output->setStatusCode(500);

        $this->assertEquals(500, $output->getStatusCode());

        $response = $output->getJsonResponse();

        $this->assertEquals(500, $response->getStatusCode());
    }

    /** @test */
    public function text_content_can_be_returned_for_editing()
    {
        $output = new Output();

        $output->editContent('Here is some content to edit.');

        $this->assertEquals(
            'Here is some content to edit.',
            $output->toArray()['edit_content']
        );
    }

    /** @test */
    public function output_can_be_converted_to_an_array()
    {
        $output = new Output();

        $this->assertIsArray($output->toArray());
    }
}
