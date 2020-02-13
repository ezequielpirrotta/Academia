<?php

namespace Tuiter\Models;

class Comment {
    protected $content;
    protected $idOwner;
    protected $idPost;
    protected $idComment;
    protected $time;
    public function __construct($time,$content,$idPost,$idOwner,$idComment){
        $this->content=$content;
        $this->idOwner=$idOwner;
        $this->time=$time;
        $this->idPost=$idPost;
        $this->idComment=$idComment;
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