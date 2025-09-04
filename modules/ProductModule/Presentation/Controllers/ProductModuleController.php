<?php

namespace Modules\ProductModule\Presentation\Controllers;

use CodeIgniter\Controller;
use Modules\ProductModule\Infrastructure\Models\ProductModuleModel;

class ProductModuleController extends Controller
{
    protected ProductModuleModel $model;

    public function __construct()
    {
        $this->model = new ProductModuleModel();
    }

    public function index()
    {
        $data['items'] = $this->model->findAll();
        return view('Modules\ProductModule\Presentation\Views\index', $data);
    }

    public function create()
    {
        return view('Modules\ProductModule\Presentation\Views\create');
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ];

        if ($this->model->insert($data)) {
            return redirect()->to('/ProductModule')
                ->with('success', 'Item created successfully!');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create item');
    }
}