<?php
namespace Controller;

use \Frame\Libs\BaseController;

final class FileController extends BaseController
 {
    public function index()
    {
        $this->upload_dir = getcwd().'\\voice\\';
        if ( !$_SESSION[ 'uid' ] ) {
            $this->json( 500, '未登录' );
        }
        $this->id = ID;
        if ( METHOD === 'POST' ) {
            // 登录
            $this->uploadFile();
        } elseif ( METHOD === 'GET' ) {
            // 检查
            $this->downFile();
        }

    }

    public function uploadFile () {

       
        //getcwd()获取当前脚本目录
        if ( !is_dir($this->upload_dir ) ) //如果目录不存在, 则创建
            mkdir( $this->upload_dir );

        function makefilename() {
            //根据上传时间生成上传文件名
            $current = getdate();
            $filename = $current[ 'year' ].$current[ 'mon' ].$current[ 'mday' ].$current[ 'hours' ].$current[ 'minutes' ].$current[ 'seconds' ].'.jpg';
            return $filename;
        }
        $newfilname = makefilename();
        $newfile = $this->upload_dir.$newfilname;
        if ( file_exists( $_FILES[ 'upfile' ][ 'tmp_name' ] ) ) {
            move_uploaded_file( $_FILES[ 'upfile' ][ 'tmp_name' ], $newfile );
            $this->json( 200, $newfilname );
        } else {
            $this->json( 500, '上传失败' );
        }
    }

    public function downFile () {
        //下载文件名
        $file_dir = $this->upload_dir;
        //下载文件存放目录
        $filename = $_GET[ 'filename' ];
        //检查文件是否存在
        if ( ! file_exists ( $file_dir . '\\' . $filename ) ) {
            header( 'HTTP/1.1 404 NOT FOUND' );
        } else {
            //以只读和二进制模式打开文件
            $file = fopen ( $file_dir . $filename, 'rb' );

            //告诉浏览器这是一个文件流格式的文件
            Header ( 'Content-type: application/octet-stream' );
            //请求范围的度量单位
            Header ( 'Accept-Ranges: bytes' );

            //Content-Length是指定包含于请求或响应中数据的字节长度
            Header ( 'Accept-Length: ' . filesize ( $file_dir . $filename ) );

            //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
            Header ( 'Content-Disposition: attachment; filename=' . $filename );

            //读取文件内容并直接输出到浏览器
            echo fread ( $file, filesize ( $file_dir . $filename ) );

            fclose ( $file );

            exit ();

        }

    }

}
