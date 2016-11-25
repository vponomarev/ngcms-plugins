<?php
$rootpath = $_SERVER['DOCUMENT_ROOT'];
@include_once $rootpath.'/engine/core.php';
@include_once root.'includes/classes/upload.class.php';

try {
    
    // Don't allow to do anything by guests
    if (!is_array($userROW)) {
        // Not authenticated, return.
        return array('status' => 0, 'errorCode' => 1, 'errorText' => '[RPC] You are not logged in');
    }

    // Now user is authenticated.
    $fmanager = new file_managment();
    $imanager = new image_managment();

    $uploadType = 'image';
    if (($uploadType != 'file') && ($uploadType != 'image')) {
        @header('HTTP/1.1 404 Wrong upload type');
        return;
    }
    
    $fmanager->get_limits($uploadType);
    $dir = $fmanager->dname;
    
    $category = '';
    $replace = 0;
    $rand = 1;

    $ures = $fmanager->file_upload(array(
        'rpc'       => 1,
        'dsn'       => 0,
        'category'  => ($category == '')?'default':$category,
        'type'      => $uploadType,
        'replace'   => $replace,
        'randprefix'=> $rand,
        'http_var'  => 'Filedata',
    ));
    
        // Now write info about image into DB
    if (is_array($sz = $imanager->get_size($dir.$ures['data']['category'].'/'.$ures['data']['name']))) {
        $fmanager->get_limits($type);
        
        // Gather filesize for thumbinals
        $thumb_size_x = 0;
        $thumb_size_y = 0;
        if (is_array($thumb) && is_readable($dir.$ures['data']['category'].'/thumb/'.$ures['data']['name']) && is_array($szt = $imanager->get_size($dir.$ures['data']['category'].'/thumb/'.$ures['data']['name']))) {
            $thumb_size_x = $szt[1];
            $thumb_size_y = $szt[2];
        }
        
        $mysql->query("update ".prefix."_".$fmanager->tname." set width=".db_squote($sz[1]).", height=".db_squote($sz[2]).", preview=".db_squote(is_array($thumb)?1:0).", p_width=".db_squote($thumb_size_x).", p_height=".db_squote($thumb_size_y).", stamp=".db_squote(is_array($stamp)?1:0)." where id = ".db_squote($ures['data']['id']));
    }
    
    $id = $ures['data']['id'];
    
    if ($irow = $mysql->record("select * from ".prefix."_images where id = ".db_squote($id))) {
        
        $folder             =   $irow['folder']?$irow['folder'].'/':'';
        $fname              =   $fmanager->dname.$folder.$irow['name'];
        $thumbname          =   $fmanager->dname.$folder.'thumb/'.$irow['name'];
        $fileurl            =   $fmanager->uname.'/'.$folder.$irow['name'];
        $thumburl           =   $fmanager->uname.'/'.$folder.'thumb/'.$irow['name'];

        $fsize          =   is_readable($fname) ? FormatSize(@filesize($fname)) : '-';
        $thumbsize      =   is_readable($thumbname) ? FormatSize(@filesize($thumbname)) : '-';

        $tvars = array(
            'id'        => $irow['id'],
            'name'      => $irow['name'],
            'orig_name' => $irow['orig_name'],
            'date'      => strftime('%d.%m.%Y %H:%M', $irow['date']),
            'author'    => $irow['user'],
            'width'     => $irow['width'],
            'height'    => $irow['height'],
            'size'      => $fsize,
            'description' => $irow['description'],
            'category'  => $irow['folder'],
            'fileurl'   => $fileurl,
            'thumburl'  => $thumburl,
            'preview_width' => $irow['p_width'],
            'preview_height' => $irow['p_height'],
            'preview_size' => $thumbsize,
            'thumb_quality' => $config['thumb_quality'],
            'thumb_size_x' => $config['thumb_size'],
            'thumb_size_y' => $config['thumb_size'],
            'r_author'  => $_REQUEST['author'],
            'r_category'    => $_REQUEST['category'],
            'r_postdate'    => $_REQUEST['postdate'],
            'r_page'    => $_REQUEST['page'],
            'r_npp'     => $_REQUEST['npp'],
        );
        
        echo json_encode($tvars);
        
    }
    
} catch (Exception $ex) {
    return "0";
}

