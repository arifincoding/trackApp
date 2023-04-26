<?php

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use App\Validations\CategoryValidation;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CategorySrvTest extends TestCase
{

    use DatabaseTransactions;

    private $repository;
    private CategoryService $service;
    private $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(CategoryRepository::class);
        $this->validator = $this->createMock(CategoryValidation::class);
        $this->service = new CategoryService($this->repository, $this->validator);
    }

    public function testShouldGetAllCategory()
    {
        $category = Category::factory()->count(3)->sequence(fn (Sequence $sequence) => ['id' => $sequence->index + 1])->make();
        $this->validator->expects($this->once())->method('query');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->repository->expects($this->once())->method('getListData')->willReturn($category);
        $result = $this->service->getAllCategory([]);
        $this->assertEquals($category->toArray(), $result);
    }

    public function testShouldGetCategoryById()
    {
        $category = Category::factory()->make(['id' => 1]);
        $this->repository->expects($this->once())->method('getDataById')->willReturn($category);
        $result = $this->service->getCategoryById(1);
        $this->assertEquals($category->toArray(), $result);
    }

    public function testShouldGetCategoryNotInResponbility()
    {
        $category = Category::factory()->count(3)->sequence(fn (Sequence $sequence) => ['id' => $sequence->index + 1])->make();
        $this->repository->expects($this->once())->method('getDataNotInResponbility')->willReturn($category);
        $result = $this->service->getCategoryNotInResponbility('2211001');
        $this->assertEquals($category->toArray(), $result);
    }

    public function testShouldNewSingleCategory()
    {
        $input = ['name' => 'test tambah kategori'];
        $category = Category::factory()->sequence($input)->make(['id' => 1]);
        $this->validator->expects($this->once())->method('post');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->repository->expects($this->once())->method('save')->willReturn($category);
        $result = $this->service->newCategory($input);
        $this->assertEquals(['category_id' => 1], $result);
    }

    public function testShouldUpdateSingleCategoryById()
    {
        $input = ['name' => 'test update kategori'];
        $category = Category::factory()->sequence($input)->make(['id' => 1]);
        $this->validator->expects($this->once())->method('post');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->repository->expects($this->once())->method('save')->willReturn($category);
        $result = $this->service->updateCategoryById($input, 1);
        $this->assertEquals(['category_id' => 1], $result);
    }

    public function testShouldDeleteSingleCategoryById()
    {
        $this->repository->expects($this->once())->method('delete')->willReturn(true);
        $result = $this->service->deleteCategoryById(1);
        $this->assertEquals('sukses hapus data kategori', $result);
    }
}
