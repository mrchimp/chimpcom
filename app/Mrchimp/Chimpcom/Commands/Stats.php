<?php 
/**
 * Get Chimpcom statistics
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use App\User;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Mrchimp\Chimpcom\Models\Feed;
use Mrchimp\Chimpcom\Models\Memory;

/**
 * Get Chimpcom statistics
 */
class Stats extends AdminCommand
{

    /**
     * Run the command
     */
    public function process() {
        $username = $this->input->get(1);
      
        if ($username != false) {
            // Individual user's stats
            $this->response->say("Finding stats for user: $username<br>");

            $user = User::where('name', $username)->first();
            
            if (!$user) {
                $this->response->error('That username does not exist.');
                return false;
            }

            $memory_count = $user->memories()->count();
            $feed_count = $user->feeds()->count();
        } else {
            // All users
            $user_count = User::count();
            $memory_count = Memory::count();
            $feed_count = Feed::count();
            $this->response->say("Users: $user_count<br>");
        }
      
        $this->response->say("Memories: $memory_count<br>");
        $this->response->say("Feeds: $feed_count<br>");
        
      
        // ======== Get files disk space usage =========
        // $this->title('File uploads <br>');
        // $dir_list = getDirList(UPLOADSDIR);
        // foreach ($dir_list as $dir) {
          // $this->response->say(str_replace(UPLOADSDIR, '', $dir['name']) . ' ' . CalcDirectorySize($dir['name']) . '<br>');
        // }
    }

    //function getDirList($dir) {
    //    // array to hold return value
    //    $retval = array();
    //    
    //    // add trailing slash if missing
    //    if(substr($dir, -1) != "/") $dir .= "/";
    //    
    //    // open pointer to directory and read list of files
    //    $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
    //    while(false !== ($entry = $d->read())) {
    //        // skip hidden files
    //        if($entry[0] == ".") continue;
    //        if(is_dir("$dir$entry")) {
    //            $retval[] = array(
    //              "name" => "$dir$entry/",
    //              "type" => filetype("$dir$entry"),
    //              "size" => 0,
    //              "lastmod" => filemtime("$dir$entry")
    //            );
    //        }/* elseif(is_readable("$dir$entry")) {
    //            $retval[] = array(
    //              "name" => "$dir$entry",
    //              "type" => mime_content_type("$dir$entry"),
    //              "size" => filesize("$dir$entry"),
    //              "lastmod" => filemtime("$dir$entry")
    //            );
    //        }*/
    //    }
    //    
    //    $d->close();
    //    
    //    return $retval;
    //}
    
    //function CalcDirectorySize($DirectoryPath) {
    //    // I reccomend using a normalize_path function here
    //    // to make sure $DirectoryPath contains an ending slash
    //    // (-> http://www.jonasjohn.de/snippets/php/normalize-path.htm)
    // 
    //    // To display a good looking size you can use a readable_filesize
    //    // function.
    //    // (-> http://www.jonasjohn.de/snippets/php/readable-filesize.htm)
    // 
    //    $Size = 0;
    //    $Dir = opendir($DirectoryPath);
    // 
    //    if (!$Dir)
    //        return -1;
    // 
    //    while (($File = readdir($Dir)) !== false) {
    //        // Skip file pointers
    //        if ($File[0] == '.') continue; 
    // 
    //        // Go recursive down, or add the file size
    //        if (is_dir($DirectoryPath . $File))            
    //            $Size += CalcDirectorySize($DirectoryPath . $File . DIRECTORY_SEPARATOR);
    //        else 
    //            $Size += filesize($DirectoryPath . $File);        
    //    }
    // 
    //    closedir($Dir);
    // 
    //    return $Size;
    //}

}