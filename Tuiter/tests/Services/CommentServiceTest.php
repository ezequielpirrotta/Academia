<?php

namespace TestTuiter\Services;
use Tuiter\Services\CommentService;
use \Tuiter\Services\UserService;
use \Tuiter\Services\FollowService;
use \Tuiter\Services\LoginService;
use \Tuiter\Services\PostService;
use \Tuiter\Models\User;
use \Tuiter\Models\Post;
final class CommentServiceTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void{
        $connection = new \MongoDB\Client("mongodb://localhost");
        $collectionComment = $connection->CommentServiceTest->CommentTest;
        $collectionUserService = $connection->CommentServiceTest->UserTest;
        $collectionPostService = $connection->CommentServiceTest->PostTest;
        $collectionPostService->drop();
        $collectionComment->drop();
        $collectionUserService->drop();
        $this->commentService=new CommentService($collectionComment);
        $this->userService = new UserService($collectionUserService);
        $this->postService= new PostService($collectionPostService);
    }
    public function testClassExists(){
        $this->assertTrue(class_exists("\Tuiter\Services\CommentService"));
    }
    public function testComment(){
        $eliel = new User("eliel", "Heber", "1234");
        $edu = new User("edu", "Edward", "1234");
        $this->userService->register("eliel", "Heber", "123456");
        $this->userService->register("edu", "Edward", "123456");
        $post=$this->postService->create('hello man',$edu);
        $this->assertTrue($post instanceof Post);
        $pudo=$this->commentService->create("boludito",$eliel,$post);
        $this->assertEquals($pudo->getContent(),"boludito");
    }
    public function testGetCommentsFromPost(){
        $eliel = new User("eliel", "Heber", "1234");
        $edu = new User("edu", "Edward", "1234");
        $this->userService->register("eliel", "Heber", "123456");
        $this->userService->register("edu", "Edward", "123456");
        $post=$this->postService->create('hello man',$edu);
        $this->assertTrue($post instanceof Post);
        $arrayDeComments=array();
        $comment1=$this->commentService->create("boludito",$eliel,$post);
        $arrayDeComments[]=$comment1;
        $comment2=$this->commentService->create("pelotudito",$eliel,$post);
        $arrayDeComments[]=$comment2;
        $comment3=$this->commentService->create("marico",$edu,$post);
        $arrayDeComments[]=$comment3;
        $comment4=$this->commentService->create("mamahuevo",$eliel,$post);
        $arrayDeComments[]=$comment4;
        $comment5=$this->commentService->create("ojete",$eliel,$post);
        $arrayDeComments[]=$comment5;
        $this->assertEquals($this->commentService->getAllCommentsFromPost($post), $arrayDeComments);
        
    }
}    