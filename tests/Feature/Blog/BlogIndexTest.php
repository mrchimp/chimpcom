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
        $home_dir = $this->createPath('/home/mrchimp/blog');
        $post = factory(File::class)->create([
            'name' => 'postname',
            'content' => 'Here is a post',
        ]);
        $home_dir->files()->save($post);

        $this->get('blog/mrchimp')
            ->assertStatus(200)
            ->assertSee('postname');
    }

    /** @test */
    public function blog_index_returns_404_if_path_is_invalid()
    {
        $this->get('blog/!!!!!!!')
            ->assertStatus(404);
    }

    /** @test */
    public function blog_index_returns_404_if_directory_doesnt_exist()
    {
        $this->get('blog/nonexistant')
            ->assertStatus(404);
    }

    /** @test */
    public function if_path_points_to_a_file_then_404_is_returned()
    {
        factory(User::class)->create([
            'name' => 'mrchimp'
        ]);
        $home_dir = $this->createPath('/home/mrchimp');
        $blog_file = factory(File::class)->create([
            'name' => 'blog',
            'content' => 'Here is a file where a directory should be',
        ]);
        $home_dir->files()->save($blog_file);

        $this->get('blog/mrchimp')
            ->assertStatus(404);
    }
}
