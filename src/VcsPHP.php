<?php

namespace jeffersonassilva\VcsPHP;

class VcsPHP
{
    /**
     * @param string $dir Directory's path of project
     * @return string
     */
    private function documentRoot($dir = null)
    {
        return $dir ? $dir : $_SERVER["DOCUMENT_ROOT"];
    }

    /**
     * Check it if vcs is GIT
     * @param string $dir Directory's path of project
     * @return bool
     */
    public static function isGIT($dir = null)
    {
        return is_dir(VcsPHP::documentRoot($dir) . "/.git") ? true : false;
    }

    /**
     * Check it if vcs is SVN
     * @param string $dir Directory's path of project
     * @return bool
     */
    public static function isSVN($dir = null)
    {
        return is_dir(VcsPHP::documentRoot($dir) . "/.svn") ? true : false;
    }

    /**
     * Show the branch name
     * @param string $dir Directory's path of project
     * @return string
     */
    public static function branch($dir = null)
    {
        $path = VcsPHP::documentRoot($dir);
        exec("cd $path && git rev-parse --abbrev-ref HEAD", $branchName);
        return current($branchName);
    }

    /**
     * Show the tag name
     * @param string $dir Directory's path of project
     * @return string
     */
    public static function tag($dir = null)
    {
        $path = VcsPHP::documentRoot($dir);
        exec("cd $path && git describe --tags --abbrev=0", $tagName);
        return array_pop($tagName);
    }

    /**
     * Show the revision code
     * @param bool $long Optional type of revision, defatul is short
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function revision($long = false, $dir = null)
    {
        $path = VcsPHP::documentRoot($dir);
        $format = $long ? 'H' : 'h';
        exec("cd $path && git log -1 --pretty=format:'%$format'", $revision);
        return current($revision);
    }

    /**
     * Show the date commit
     * @param string $format Optional format of date, default is american format '%Y-%m-%d %H:%M:%S'
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function dateCommit($format = '%Y-%m-%d %H:%M:%S', $dir = null)
    {
        $path = VcsPHP::documentRoot($dir);
        exec("cd $path && git log -1 --pretty='format:%cd' --date=format:'$format'", $dateCommit);
        return current($dateCommit);
    }

    /**
     * Show the author commit
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function authorCommit($dir = null)
    {
        $path = VcsPHP::documentRoot($dir);
        exec("cd $path && git log -1 --pretty='format:%an'", $authorCommit);
        return current($authorCommit);
    }
}