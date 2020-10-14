<?php

use Illuminate\Database\Migrations\Migration;
use Mrchimp\Chimpcom\Models\Directory;

class RemoveRootDirectory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $root = Directory::whereIsRoot()->first();

        if ($root) {
            $root->fixTree();
            $root->refreshNode();

            $root->children->each(function ($child) use ($root) {
                $child->saveAsRoot();
            });

            $root->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $children = Directory::whereIsRoot()->get();

        $root = Directory::create([
            'name' => 'root',
        ]);

        $children->each(function ($child) use ($root) {
            $root->appendNode($child);
        });

        $root->refreshNode();
    }
}
