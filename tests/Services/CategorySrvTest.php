<?php

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use App\Validations\CategoryValidation;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CategorySrvtest extends TestCase
{

    use DatabaseMigrations;

    private CategoryRepository $repository;
    private CategoryService $service;
    private CategoryValidation $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(CategoryRepository::class);
        $this->validator = $this->createMock(CategoryValidation::class);
        $this->service = new CategoryService($this->repository, $this->validator);
    }

    public function testShouldGetAllCategory()
    {
        $category = Category::factory()->count(3)->create();
        $this->validator->expects($this->once())->method('query');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->repository->expects($this->once())->method('getListData')->willReturn($category);
        $result = $this->service->getAllCategory([]);
        $this->assertEquals($category->toArray(), $result);
    }

    public function testShouldGetCategoryById()
    {
        $category = Category::factory()->create();
        $category->idKategori = $category->id;
        unset($category->id);
        $this->repository->expects($this->once())->method('getDataById')->willReturn($category);
        $result = $this->service->getCategoryById(1);
        $this->assertEquals($category->toArray(), $result);
    }

    public function testShouldGetCategoryNotInResponbility()
    {
        $category = Category::factory()->count(3)->create();
        $this->repository->expects($this->once())->method('getDataNotInResponbility')->willReturn($category);
        $result = $this->service->getCategoryNotInResponbility('2211001');
        $this->assertEquals($category->toArray(), $result);
    }

    public function testShouldNewSingleCategory()
    {
        $category = Category::factory()->make(['id' => 1, 'nama' => 'test ctgr']);
        $this->validator->expects($this->once())->method('post');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->repository->expects($this->once())->method('save')->willReturn($category);
        $result = $this->service->newCategory(['nama' => 'test ctgr']);
        $this->assertEquals(['idKategori' => 1], $result);
    }

    public function testShouldUpdateSingleCategoryById()
    {
        $category = Category::factory()->make(['id' => 1, 'nama' => 'test ctgr']);
        $this->validator->expects($this->once())->method('post');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->repository->expects($this->once())->method('save')->willReturn($category);
        $result = $this->service->updateCategoryById(['nama' => 'test ctgr'], 1);
        $this->assertEquals(['idKategori' => 1], $result);
    }

    public function testShouldDeleteSingleCategoryById()
    {
        $this->repository->expects($this->once())->method('delete')->willReturn(true);
        $result = $this->service->deleteCategoryById(1);
        $this->assertEquals('sukses hapus data kategori', $result);
    }
}