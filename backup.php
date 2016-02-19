<?php
//初期設定
//http://www60.atwiki.jp/kassimine/の60がサーバーID、kassimineがサイトID
//サーバーID
$serverid = 60;
//サイトID
$siteid = "";
//エラー回数
//ページがこれ以上なくなったと判断する回数
//歯抜けのページがあまりにも多い場合はスキップで飛ばしてしまうべき
//デフォルトは5回
$error = 5;
//スキップするページID
//エラー回数よりも多くの存在しないページがある場合はここでスキップを設定していた方がよい
$skip = array(3,4,5,6,7,8,9,10,11,12,13,14);

//タイムアウトを防ぐため
set_time_limit(0);
//file_get_contentsが使えない鯖だったのでcurl_get_contentsを定義
function curl_get_contents( $url, $timeout = 60 ){
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_HEADER, false );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
    //curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    $result = curl_exec( $ch );
    curl_close( $ch );
    return $result;
}
$e = 0;
$id = 1;
while($e < $error){
   if(in_array($id, $skip)){
      echo $id."Skip!<br>\n";
      $id++;
      
      sleep(2);
      continue;
   }
   $url = "http://www".$serverid.".atwiki.jp/".$siteid."/?cmd=backup&action=source&pageid=".$id;
   $html = curl_get_contents($url);
   if(strpos($html, "<pre class=\"cmd_backup\" style=\"overflow:scroll;\" >") === false){
   echo $id." Err<br>";
   $id++;
   $e++;
   continue;
   }
$html = substr($html, 0, strpos($html, "</pre>"));
$h = substr($html, 0, strpos($html, "<pre class=\"cmd_backup\" style=\"overflow:scroll;\" >")+strlen("<pre class=\"cmd_backup\" style=\"overflow:scroll;\" >"));
$html = str_replace($h, "", $html);
file_put_contents("./data/".$id.".txt",$html);
echo $id." Success<br>";
$id++;
//連続アクセスで制限されるのを防ぐため毎アクセス後に2秒待機
sleep(5);
}
echo "ErrCount:".$error."! exited!";
