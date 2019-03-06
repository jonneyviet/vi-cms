<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Packages\Media\Repositories\Eloquent\FolderRepository;
class MediaTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function __construct(){
    	
    }
    public function testSearch(){
    	$folder = new FolderRepository();
    	$folder->search("test");
    }
}
