<?php
require 'SocrataTest.php';
$test = new SocrataTest();
?>
<h2><?=get_class($test)?></h2>
<table>
<?
foreach(get_class_methods($test) as $method){
  if($method == '__construct'){
    break;
  }
  ?>
  <tr>
    <td><?=$method?></td>
    <td>
  <?
  try {
      $return = $test->$method();
      if($return === NULL){
        echo 'PASS';
      }
      else {
        echo 'FAIL';
      }
  }
  catch(Exception $e){
    // Expected exceptions
    $exception_message = $e->getMessage();
    $exception_code = $exception_message[7] . $exception_message[8] . $exception_message[9];

    if($exception_code == '400' && $method == 'test_query_error'){
      echo 'PASS';
    }
    else if ($exception_code != '400' && $method == 'test_query_error'){
      echo 'FAIL';
    }

    if($exception_code == '403' && $method == 'test_upsert'){
      echo 'PASS';
    }
    else if($exception_code != '403' && $method == 'test_upsert'){
      echo 'FAIL';
    }
  }
}
 ?>
</td>
</tr>
</table>
<style>
  table tr td  { padding: 5px; }
</style>
