<?php

namespace Tuiter\Services;
use \Tuiter\Models\User;
use \Tuiter\Models\Post;
use \Tuiter\Models\Comment;
use \Tuiter\Models\CommentNull;
class CommentService {

    private $collection;

    public function __construct($collection){
        $this->collection = $collection;
    }
    public function create(string $content, User $user, Post $post){
        $comment=new Comment(time(),$content,$post->getPostId(),$user->getUserId(),md5(microtime()));
        $result=$this->collection->insertOne(
            array(
                "content"=>$comment->getContent(),
                "idOwner"=>$comment->getIdOwner(),
                "idPost"=>$comment->getIdPost(),
                "idComment"=>$comment->getIdComment(),
                "time"=>$comment->getTime()
            )
        );
        if($result->getInsertedCount()!=1){
            return new CommentNull(time(),$content,$post->getPostId(),$user->getUserId(),md5(microtime()));
        }
        return $comment;
    }
    public function getAllCommentsFromPost(Post $post){
        $comments=array();
        $result=$this->collection->find(
            array(
                'idPost'=>$post->getPostId()
            )
        );
        foreach($result as $info){
            $newComment=new Comment(
                $info["time"],
                $info["content"],
                $info["idPost"],
                $info["idOwner"],
                $info["idComment"]
            );
            $comments[]=$newComment;
        }
        return $comments;
    }
    public function getRandomComment(Post $post){
        $comments=array();
        $result=$this->collection->find(
            array(
                'idPost'=>$post->getPostId()
            )
        );
            $newComment=new Comment(
                $result["time"],
                $result["content"],
                $result["idPost"],
                $result["idOwner"],
                $result["idComment"]
            );
            return $newComment;
            }

}