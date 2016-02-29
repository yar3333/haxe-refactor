<?php
$typeNamesMapping = array(
     'int' => 'Int'
    ,'integer' => 'Int'
    ,'float' => 'Float'
    ,'bool' => 'Bool'
    ,'boolean' => 'Bool'
    ,'string' => 'String'
    ,'array' => 'NativeArray'
    ,'mixed' => 'Dynamic'
    ,'mixes' => 'Dynamic'
    ,'object' => 'Dynamic'
    ,'void' => 'Void'
    ,'resource' => 'Resource'
    
    ,'true' => 'Bool'
    ,'false' => 'Bool'
);

$varNamesMapping = array(
     '_POST' => '/*$_POST*/php.Web.getParams()'
    ,'_GET' => '/*$_GET*/php.Web.getParams()'
    ,'__FILE__' => "untyped __php__('__FILE__')"
    ,'_SESSION' => "php.Session"
);

$magickFunctionNameMapping = array(
     '__construct'              => 'new'
);

$functionNameMapping = array(
     'str_starts_with'          => array('(',0,')', '.', 'startsWith', '(', 1, ')')
    ,'str_ends_with'            => array('(',0,')', '.', 'endsWith', '(', 1, ')')

    ,'ltrim'                    => array('(',0,')', '.', 'ltrim', '(', 1, ')')
    ,'rtrim'                    => array('(',0,')', '.', 'rtrim', '(', 1, ')')
    ,'trim'                     => array('(',0,')', '.', 'trim', '(', 1, ')')
    ,'strip_tags'               => 'StringTools.stripTags'
    ,'sprintf'                  => 'StringTools.format'
    ,'str_pad'                  => 'StringTools.pad'
    
    ,'abs'                      => 'Math.abs'
    ,'round'                    => 'Math.round'
    ,'min'                      => 'Math.min'
    ,'max'                      => 'Math.max'
    ,'pow'                      => 'Math.pow'
    ,'sqrt'                     => 'Math.sqrt'

    //,'htmlspecialchars'         => 'StringTools.htmlEscape'
    //,'htmlspecialchars_decode'  => 'StringTools.htmlUnescape'

    ,'serialize'                => 'php.Lib.serialize'
    ,'unserialize'              => 'php.Lib.unserialize'

    ,'header'                   => 'php.Web.setHeader'

    ,'dirname'                  => 'sys.io.Path.directory'
    ,'file_get_contents'        => 'sys.io.File.getContent'
    ,'file_put_contents'        => 'sys.io.File.saveContent'
    ,'file_exists'              => 'sys.FileSystem.exists'
    ,'is_file'                  => array('sys.FileSystem.exists(', 0, ') && !sys.FileSystem.isDirectory(', 0, ')')
    ,'is_dir'                   => array('sys.FileSystem.exists(', 0, ') && sys.FileSystem.isDirectory(', 0, ')')
    ,'realpath'                 => 'sys.FileSystem.fullPath'
    ,'unlink'                   => 'sys.FileSystem.deleteFile'
    ,'mkdir'                    => 'sys.FileSystem.createDirectory'
    ,'filemtime'                => array('sys.FileSystem.stat(',0,').mtime.getTime()')

    ,'microtime'                => 'Sys.time'
    ,'time'                     => 'Sys.time'

    ,'substr'                   => array('(',0,')', '.', 'substr', '(', 1, ', ', 2, ')')
    ,'strlen'                   => array('(',0,')', '.', 'length')
    ,'str_replace'              => array('(',2,')', '.', 'replace', '(', 0, ', ', 1, ')')
    ,'strpos'                   => array('(',0,')', '.', 'indexOf', '(', 1, ', ', 2, ')')
    ,'strrpos'                  => array('(',0,')', '.', 'lastIndexOf', '(', 1, ', ', 2, ')')
    ,'strtolower'               => array('(',0,')', '.', 'toLowerCase', '(', ')')
    ,'strtoupper'               => array('(',0,')', '.', 'toUpperCase', '(', ')')
    ,'json_encode'              => 'StringTools.jsonEncode'
    ,'json_decode'              => 'StringTools.jsonDecode'


    ,'count'                    => array('(',0,')', '.', 'length')
    ,'explode'                  => array('(',1,')', '.', 'split', '(', 0, ')')
    ,'implode'                  => array('(',1,')', '.', 'join', '(', 0, ')')
    ,'array_slice'              => array('(',0,')', '.', 'slice', '(', 1, ',', 2, ')')
    ,'array_splice'             => array('(',0,')', '.', 'splice', '(', 1, ', ', 2, ')')
    ,'array_push'               => array('(',0,')', '.', 'push', '(', 1, ')')
    ,'array_pop'                => array('(',0,')', '.', 'pop', '(', 1, ')')
    ,'array_shift'              => array('(',0,')', '.', 'shift', '(', 1, ')')
    ,'array_unshift'            => array('(',0,')', '.', 'unshift', '(', 1, ')')

    ,'array_search'            => array('(',1,')', '.', 'indexOf', '(', 0, ')')
    //,'array_key_exists'        => array('(',1,')', '.', 'exists', '(', 0, ')')

    //,'array_keys'              => array('(',0,')', '.', 'keys()')

    //,'method_exists'           => array('Reflect.hasMethod(Type.resolveClass_getClass(',0,'), ',1,')')
    //,'class_exists'            => 'Type.resolveClass'

    ,'exit'                    => 'Sys.exit'
    
    // GD to ImageMagick
    //,'imagesx'                 => array('(',0,')', '.', 'getImageWidth()')
    //,'imagesy'                 => array('(',0,')', '.', 'getImageHeight()')
    //,'imagecreatetruecolor'    => 'new Imagick'
    
    ,'sha1'                     => 'haxe.crypto.Sha1.encode'
);
