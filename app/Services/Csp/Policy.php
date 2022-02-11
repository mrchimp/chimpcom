<?php

namespace App\Services\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Basic as BasePolicy;

class Policy extends BasePolicy
{
    public function configure()
    {
        $this
            ->addGeneralDirectives();
    }

    protected function addGeneralDirectives(): self
    {
        return $this
            ->addDirective(Directive::BASE, 'self')
            ->addDirective(Directive::FORM_ACTION, [
                'deviouschimp.co.uk',
                'chimpcom.test',
            ])
            ->addDirective(Directive::IMG, [
                '*',
                'data:',
            ])
            ->addDirective(Directive::OBJECT, 'none');
    }
}
