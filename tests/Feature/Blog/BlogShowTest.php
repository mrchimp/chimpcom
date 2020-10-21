<?php

namespace Tests\Feature\Blog;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class BlogShowTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function blog_post_can_be_viewed()
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

        $this->get('blog/mrchimp/postname')
            ->assertStatus(200)
            ->assertSee('Here is a post');
    }
}
