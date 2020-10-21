<?php

namespace Tests\Feature\Blog;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class BlogIndexTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function blog_index_can_be_accessed()
    {
        factory(User::class)->create([
            'name' => 'mrchimp'
        ]);
        $blog_dir = $this->createPath('/home/mrchimp/blog');
        $post = factory(File::class)->create([
            'name' => 'postname',
            'content' => 'Here is a post',
        ]);
        $blog_dir->files()->save($post);

        $this->get('blog/mrchimp')
            ->assertStatus(200)
            ->assertSee('postname');
    }
}
