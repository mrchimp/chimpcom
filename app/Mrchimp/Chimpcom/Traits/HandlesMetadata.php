<?php

namespace Mrchimp\Chimpcom\Traits;

trait HandlesMetadata
{

    protected function parseMeta($meta = [])
    {
        return array_reduce($meta, function ($carry, $item) {
            $parts = explode(':', $item, 2);

            if (count($parts) < 2) {
                return $carry;
            }

            $carry[$parts[0]] = $parts[1];

            return $carry;
        }, []);
    }
}
