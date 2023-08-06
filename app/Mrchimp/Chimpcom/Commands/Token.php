<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Facades\Format;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Validator;

class Token extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('token');
        $this->setDescription('Manage API tokens.');
        $this->addOption(
            'create',
            'c',
            null,
            'Create a new token.'
        );
        $this->addOption(
            'revoke',
            'r',
            null,
            'Revoke a token.'
        );
        $this->addArgument(
            'name',
            InputArgument::OPTIONAL,
            'Name of the token to create/revoke'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));
            return 1;
        }

        if ($input->getOption('create')) {
            return $this->createToken($input, $output);
        }

        if ($input->getOption('revoke')) {
            return $this->revokeToken($input, $output);
        }

        return $this->listTokens($input, $output);
    }

    /**
     * Create a new API Token
     */
    protected function createToken(InputInterface $input, OutputInterface $output): int
    {
        // @todo prevent creating too manny tokens

        $name = $input->getArgument('name');

        $validator = Validator::make([
            'name' => $name,
        ], [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $output->error('No token name provided.');
            return 1;
        }

        $token = Auth::user()->createToken($input->getArgument('name'));

        $output->write('Here is your new token. Keep it safe. It won\'t be shown again.' . Format::nl(2));
        $output->write('<code>' . $token->plainTextToken . '</code>');

        return 0;
    }

    /**
     * Revoke an API token
     */
    protected function revokeToken(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        $validator = Validator::make([
            'name' => $name,
        ], [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $output->error('No token name provided.');
            return 1;
        }

        Auth::user()->tokens()->where('name', $name)->delete();

        $output->write('Ok.');

        return 0;
    }

    /**
     * List available API tokens
     */
    protected function listTokens(InputInterface $input, OutputInterface $output): int
    {
        $tokens = Auth::user()->tokens;

        if ($tokens->isEmpty()) {
            $output->error('You have no tokens.');
            return 2;
        }

        $output->title('Name - Created At', true);
        $tokens->each(function ($token) use ($output) {
            $output->write($token->name . ' - ' . $token->created_at->diffForHumans() . Format::nl());
        });

        return 0;
    }
}
