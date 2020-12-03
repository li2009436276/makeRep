<?php 
function tne($code,$extra=[]){
    throw new \MakeRep\Exceptions\ApiException($code,$extra);
}