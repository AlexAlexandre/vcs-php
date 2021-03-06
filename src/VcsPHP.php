<?php

namespace jeffersonassilva\VcsPHP;

class VcsPHP
{
    /**
     * @param string $dir Directory's path of project
     * @return string
     */
    private static function documentRoot($dir = null)
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
     * Show repository url
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function repository($dir = null)
    {
        return VcsPHP::run([
            'git' => "git remote get-url origin",
            'svn' => "svn info --show-item url"
        ], $dir);
    }

    /**
     * Show the branch name
     * @param string $dir Directory's path of project
     * @return string
     */
    public static function branch($dir = null)
    {
        return VcsPHP::run([
            'git' => "git rev-parse --abbrev-ref HEAD",
            'svn' => "svn info | grep '^URL:' | egrep -o '(branches)/[^/]+' | egrep -o '[^/]+$'"
        ], $dir);
    }

    /**
     * Show the tag name
     * @param string $dir Directory's path of project
     * @return string
     */
    public static function tag($dir = null)
    {
        return VcsPHP::run([
            'git' => "git describe --tags --abbrev=0",
            'svn' => "svn info | grep '^URL:' | egrep -o '(tags)/[^/]+' | egrep -o '[^/]+$'"
        ], $dir);
    }

    /**
     * Show the revision code
     * @param bool $long Optional type of revision, defatul is short
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function revision($long = false, $dir = null)
    {
        $format = $long ? 'H' : 'h';
        return VcsPHP::run([
            'git' => "git log -1 --pretty=format:'%$format'",
            'svn' => "svn info --show-item last-changed-revision"
        ], $dir);
    }

    /**
     * Show the author date
     * @param string $format Format of date, default is american format '%Y-%m-%d %H:%M:%S'
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function authorDate($format = 'Y-m-d H:i:s', $dir = null)
    {
        $formatGit = VcsPHP::formatDateToGit($format);
        return VcsPHP::run([
            'git' => "git log -1 --pretty='format:%ad' --date=format:'$formatGit'",
            'svn' => "svn info --show-item last-changed-date"
        ], $dir, $format);
    }

    /**
     * Show the name of author
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function authorName($dir = null)
    {
        return VcsPHP::run([
            'git' => "git log -1 --pretty='format:%an'",
            'svn' => "svn info --show-item last-changed-author"
        ], $dir);
    }

    /**
     * Show the email of author
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function authorEmail($dir = null)
    {
        return VcsPHP::run([
            'git' => "git log -1 --pretty='format:%ae'",
        ], $dir);
    }

    /**
     * Show the date of committer
     * @param string $format Optional format of date, default is american format '%Y-%m-%d %H:%M:%S'
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function committerDate($format = 'Y-m-d H:i:s', $dir = null)
    {
        $format = VcsPHP::formatDateToGit($format);
        return VcsPHP::run([
            'git' => "git log -1 --pretty='format:%cd' --date=format:'$format'",
        ], $dir);
    }

    /**
     * Show the name of committer
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function committerName($dir = null)
    {
        return VcsPHP::run([
            'git' => "git log -1 --pretty='format:%cn'",
        ], $dir);
    }

    /**
     * Show the email of committer
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function committerEmail($dir = null)
    {
        return VcsPHP::run([
            'git' => "git log -1 --pretty='format:%ce'",
        ], $dir);
    }

    /**
     * Show the subject commit
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function subject($dir = null)
    {
        return VcsPHP::run([
            'git' => "git log -1 --pretty='format:%s'",
        ], $dir);
    }

    /**
     * It show the quantity of commits
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function commits($merges = false, $dir = null)
    {
        $mergesParam = !$merges ? '--no-merges' : '';
        return VcsPHP::run([
            'git' => "git rev-list $mergesParam --count HEAD",
        ], $dir);
    }

    /**
     * Show repository url
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function uuid($dir = null)
    {
        return VcsPHP::run([
            'svn' => "svn info --show-item repos-uuid"
        ], $dir);
    }

    /**
     * Show the node kind
     * @param string $dir Directory's path of project
     * @return mixed
     */
    public static function nodeKind($dir = null)
    {
        return VcsPHP::run([
            'svn' => "svn info --show-item kind"
        ], $dir);
    }

    /**
     * @param $format
     * @return mixed
     */
    private static function formatDateToGit($format)
    {
        $format = str_replace(array('i', 's'), array('M', 'S'), $format);
        $format = preg_replace('/[a-zA-Z]/', '%$0', $format);
        return $format;
    }

    /**
     * @param $dateCommit
     * @param $format
     * @return array
     */
    private static function formatDateToSvn($dateCommit, $format)
    {
        $arrayDate = array();
        foreach ($dateCommit as $key => $dt) {
            $arrayDate[$key] = date($format, strtotime($dt));
        }
        return $arrayDate;
    }

    /**
     * @param $command
     * @return \stdClass
     */
    private static function cmds($command)
    {
        $cmd = new \stdClass();
        $cmd->git = isset($command['git']) ? $command['git'] : null;
        $cmd->svn = isset($command['svn']) ? $command['svn'] : null;
        return $cmd;
    }

    /**
     * @param $command
     * @param $dir
     * @param string $format
     * @return mixed
     */
    private static function run($command, $dir, $format = '')
    {
        $path = VcsPHP::documentRoot($dir);
        $cmd = VcsPHP::cmds($command);

        if (VcsPHP::isGIT($dir)) {
            exec("cd $path && $cmd->git", $data);
        } elseif (VcsPHP::isSVN($dir)) {
            exec("cd $path && $cmd->svn", $data);
            if (!empty($format)) {
                $data = VcsPHP::formatDateToSvn($data, $format);
            }
        }
        return current($data);
    }
}
