<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\CategoryContract;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Http\Controllers\BaseController;
use Mail;
use Auth;

class CategoryController extends BaseController
{
    /**
     * @var CategoryContract
     */
    protected $categoryRepository;


    /**
     * CategoryController constructor.
     * @param CategoryContract $categoryRepository
     */
    public function __construct(CategoryContract $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index(Request $request)
    {
        $categories = $this->categoryRepository->AllCategoryList();
        $this->setPageTitle('All Categories', 'List of all categories');
        return view('admin.category.index', compact('categories'));
    }
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->setPageTitle('Category', 'Create category');
        return view('admin.category.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      =>  'required|max:191',
            'image'     =>  'required|mimes:jpg,jpeg,png|max:2000',
            // 'meta_title'      =>  'required',
            // 'meta_keyword'      =>  'required',
            // 'meta_description'      =>  'required',
        ]);

        $params = $request->except('_token');
        $design = $this->categoryRepository->createCategory($params);

        if (!$design) {
            return $this->responseRedirectBack('Error occurred while creating category.', 'error', true, true);
        }
        return $this->responseRedirect('admin.category.index', 'Category added successfully' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $targetDesign = $this->categoryRepository->findCategoryById($id);
        
        $this->setPageTitle('Category', 'Edit Category : '.$targetDesign->title);
        return view('admin.category.edit', compact('targetDesign'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'name'      =>  'required|max:191',
        ]);

        $params = $request->except('_token');

        $design = $this->categoryRepository->updateCategory($params);

        if (!$design) {
            return $this->responseRedirectBack('Error occurred while updating Category.', 'error', true, true);
        }
        return $this->responseRedirectBack('Category updated successfully' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $design = $this->categoryRepository->deleteCategory($id);

        if (!$design) {
            return $this->responseRedirectBack('Error occurred while deleting category.', 'error', true, true);
        }
        return $this->responseRedirect('admin.category.index', 'Category has been deleted successfully' ,'success',false, false);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateStatus(Request $request){

        $params = $request->except('_token');

        $banner = $this->categoryRepository->updateCategoryStatus($params);

        if ($banner) {
            return response()->json(array('message'=>'Category status successfully updated'));
        }
    }
}
