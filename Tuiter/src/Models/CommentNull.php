<?php

namespace Tuiter\Models;

class CommentNull extends Comment{
    protected $content;
    protected $idOwner;
    protected $idPost;
    protected $idComment;
    protected $time;
    public function __construct($time,$content,$idPost,$idOwner,$idComment){
        $this->content="";
        $this->idOwner=0;
        $this->time=0;
        $this->idPost=0;
        $this->idComment=0;
    }
    public function getContent(){
        return $this->content;
    }
    public function getIdOwner(){
        return $this->idOwner;
    }
    public function getIdPost(){
        return $this->idPost;
    }
    public function getIdComment(){
        return $this->idComment;
    }
    public function getTime(){
        return $this->time;
    }
    
}