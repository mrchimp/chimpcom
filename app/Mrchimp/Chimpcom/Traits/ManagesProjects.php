<?php

namespace Mrchimp\Chimpcom\Traits;

use Auth;

trait ManagesProjects
{
    protected function projectFromName($project_name)
    {
        if ($project_name) {
            return Auth::user()
                ->projects()
                ->nameOrId($project_name)
                ->first();
        } else {
            return null;
        }
    }
}
